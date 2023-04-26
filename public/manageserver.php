<?php
   //Szerverek kezelése
   session_start();
   if(!isset($_SESSION['username']))
   {
      header('Location: /login');
      exit();
   }
      
   require '../config/components.php';
   require '../src/remote.class.php';

   $id = $_GET['id'];
   $db = new DBconnect();
   $remote = new RemoteManager(); 
   
   //Tűzfal aktiváció
   require '../includes/integrated_fw.php';

   //Jogosultság lekérdezése
   $account = $db->query('SELECT * FROM accounts LEFT JOIN permissions 
   ON accounts.id = permissions.user_id WHERE accounts.username = ?', 
   array($_SESSION['username']))->fetchArray();
   $permission = $account['role'];
   
   //Szerver adatai
   $server = $db->query('SELECT * FROM remote_servers WHERE id = ?', 
   array($_GET['id']))->fetchArray();
   $server_id = $server['id'];
   $ip_address = $server['ip_address'];
   $ssh_port = $server['ssh_port'];
   $hash_file = $server['server_id'];
   
   //GEOIP adatok a helymeghatározáshoz
   //geoplugin.net API használatával
   $ip = $server['ip_address']; 
   $url = file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}");
   $getInfo = json_decode($url);     
   $lat = $getInfo->geoplugin_latitude;
   $long = $getInfo->geoplugin_longitude;

   //Státusz lekérdezése
   $status = $remote->status($ip_address, $ssh_port);

   //CPU használat
   $cpu_usage = $remote->ssh_connect($ip_address, $ssh_port, $hash_file, 
   $command = "grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'");

   //Memória használat
   $ram_usage = $remote->ssh_connect($ip_address, $ssh_port, $hash_file, 
   $command = "free | grep Mem | awk '{print $3/$2 * 100.0}'");

   $current_hostname = $remote->ssh_connect($ip_address, $ssh_port, $hash_file, $command = "hostname");

   //Leállítás
   if(isset($_POST['btn-shutdown']))
   {
      //Értesítés
      $server_stopped = $lang['CommandSent'];

      //Naplózás
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO server_logs (server_id, user_id, user_ip, action) 
      VALUES (?,?,?,?)', $server_id, $user_id, $user_ip, "Szerver újraindítása");

      //Parancs küldése
      $remote->ssh_connect($ip_address, $ssh_port, $hash_file, 
      $command = "/usr/bin/sudo poweroff > /dev/null 2>&1");

   }

   //Újraindítás
   if(isset($_POST['btn-reboot']))
   {
      //Értesítés
      $server_restarted = $lang['CommandSent'];

      //Naplózás
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO server_logs (server_id, user_id, user_ip, action) 
      VALUES (?,?,?,?)', $server_id, $user_id, $user_ip, "Szerver leállítása");

      //Parancs küldése
      $remote->ssh_connect($ip_address, $ssh_port, $hash_file, 
      $command = "/usr/bin/sudo shutdown -n now > /dev/null 2>&1");

   }

   //Root jelszó váltás
   $error = false;
   if(isset($_POST['btn-changepwd']))
   {
      $rootpwd = trim($_POST['rootpwd']);    
      $rootpwd = htmlspecialchars(strip_tags($rootpwd));

      $pwdagain = trim($_POST['pwdagain']);    
      $pwdagain = htmlspecialchars(strip_tags($pwdagain));

      //Hibakezelés
      //Üres jelszó
      if(empty($rootpwd))
      {
         $error = true;
         $emptyPwd = $lang['FieldEmpty'];
      }

      if(empty($pwdagain))
      {
         $error = true;
         $emptyPwdAgain = $lang['FieldEmpty'];
      }

      //Jelszó követelmények
      $pwd_requirement = '/^(?=.*[A-Z]).{9,}$/';
      if(!preg_match($pwd_requirement, $rootpwd))
      {
         $error = true;
         $notStrenght = $lang['PwdStrength'];
      }

      //Egyeznek-e a jelszavak
      if($rootpwd !== $pwdagain)
      {
         $error = true;
         $notMatch = $lang['PwdNotMatch'];
      }

      if(!$error)
      {

         //Parancs elküldése
         $remote->ssh_connect($ip_address, $ssh_port, $hash_file, 
         $command = "/usr/bin/sudo echo -e '{$rootpwd}\n{$rootpwd}' | passwd root > /dev/null 2>&1");
         $rootchgd = base64_encode($rootpwd);
         shell_exec("/usr/bin/sudo sh -c 'echo {$rootchgd} > /var/www/ssh_keys/{$hash_file}.key'");

         //Naplózás
         $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
         array($_SESSION['username']))->fetchArray();
         $user_id = $log['id'];
         $user_ip = real_ip();
         $insert = $db->query('INSERT INTO server_logs (server_id, user_id, user_ip, action) 
         VALUES (?,?,?,?)', $server_id, $user_id, $user_ip, "Szerver root jelszó megváltoztatása");

         //Értesítés
         $pwdchanged = $lang['RootPwdChanged'];

      }
   }

   //Hostname váltás
   $hnerror = false;
   if(isset($_POST['btn-changehn']))
   {

      $hostname = trim($_POST['hostname']);    
      $hostname = htmlspecialchars(strip_tags($hostname));

      if(empty($hostname))
      {
         $hnerror = true;
         $emptyHostname = $lang['FieldEmpty'];
      }

      if(!$hnerror)
      {

         //Parancs küldése
         $remote->ssh_connect($ip_address, $ssh_port, $hash_file, 
         $command = "/usr/bin/sudo hostnamectl set-hostname {$hostname} > /dev/null 2>&1");

         //Naplózás
         $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
         array($_SESSION['username']))->fetchArray();
         $user_id = $log['id'];
         $user_ip = real_ip();
         $insert = $db->query('INSERT INTO server_logs (server_id, user_id, user_ip, action) 
         VALUES (?,?,?,?)', $server_id, $user_id, $user_ip, "Szerver hostname megváltoztatása");

         //Értesítés
         $hnchanged = $lang['HostnameChanged'];

      }
   }

   //Szerver törlése
   if(isset($_POST['btn-delete']))
   {

      //Naplózás
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO server_logs (server_id, user_id, user_ip, action) 
      VALUES (?,?,?,?)', $server_id, $user_id, $user_ip, "Szerver törlése");

      //Jelszó törlése 
      shell_exec("sudo rm -rf /var/www/ssh_keys/{$hash_file}.key");

      //Törlés az adatbázisból
      $db->query("DELETE FROM remote_servers WHERE id = '$id'");

      //Átirányítás
      header('Location: /servers');

   }

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title>
         <?php echo $lang['ManageServer']; ?>
         (<?php echo htmlspecialchars($server['ip_address']); ?>)
      </title>
   </head>
   <body class="bg-light">
      <?php include '../includes/navbar.php'; ?>
      <div class="row">
         <div class="col-sm-3 py-5">
            <?php include '../includes/sidebar.php'; ?>
         </div>
         <div class="col-sm-9 container-fluid py-5">
            <div class="card w-100">
               <div class="card-body py-5">
                  <h3>
                     <i class="fas fa-wrench"></i> 
                     <?php 
                        echo $lang['ManageServer']; ?>
                     (<?php echo htmlspecialchars($server['ip_address']); ?>)
                  </h3>
                  <div class="breadcrumb">
                     <?php echo $lang['ServerStatus']; ?>: 
                     <?php 
                        if($status == "online")
                        { ?> 
                     <span class="text-success text-bold">
                     <i class="fas fa-circle"></i> 
                     Online
                     </span>
                     <?php } else { ?>
                     <span class="text-danger text-bold">
                     <i class="fas fa-circle"></i> 
                     Offline
                     </span>
                     <?php } ?>
                  </div>
                  <?php if($server_stopped)
                     { ?>
                  <div class="alert alert-success">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $lang['CommandSent']; ?>
                  </div>
                  <?php }
                     if($server_restarted)
                     { ?>
                  <div class="alert alert-success">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $lang['CommandSent']; ?>
                  </div>
                  <?php } 
                     if (isset($notStrenght))
                     { ?>
                  <div class="alert alert-danger">
                     <i class="fas fa-exclamation-triangle"></i>
                     <?php echo $notStrenght; ?>
                  </div>
                  <?php } 
                     if(isset($notMatch)) 
                     { ?>
                  <div class="alert alert-danger">
                     <i class="fas fa-exclamation-triangle"></i>
                     <?php echo $notMatch; ?>
                  </div>
                  <?php } 
                     if(isset($pwdchanged))
                     { ?> 
                  <div class="alert alert-success">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $pwdchanged; ?>
                  </div>
                  <?php } 
                     if (isset($hnchanged))
                     { ?> 
                  <div class="alert alert-success">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $hnchanged; ?>
                  </div>
                  <?php } ?>
                  <ul class="nav nav-tabs" style="margin-top: 25px; margin-bottom: 25px;">
                     <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#details">
                        <?php echo $lang['Details']; ?>
                        </a>
                     </li>
                     <li class="nav-item" <?php if($permission == 0) { echo "style='display: none;'"; } ?>> 
                        <a class="nav-link" data-toggle="tab" href="#rootpwd">
                        <?php echo $lang['RootPwd']; ?>
                        </a>
                     </li>
                     <li class="nav-item" <?php if($permission == 0) { echo "style='display: none;'"; } ?>>
                        <a class="nav-link" data-toggle="tab" href="#hostname">
                        <?php echo $lang['ChangeHostname']; ?>
                        </a>
                     </li>
                     <li class="nav-item" <?php if($permission == 0) { echo "style='display: none;'"; } ?>>
                        <a class="nav-link" data-toggle="tab" href="#delete">
                        <?php echo $lang['DeleteServer']; ?>
                        </a>
                     </li>
                  </ul>
                  <div class="tab-content">
                     <div class="tab-pane container active" id="details">
                        <div class="row">
                           <div class="col-sm-6">
                              <div class="row">
                                 <?php 
                                    if($permission !== 0)
                                    {
                                    ?>
                                 <div class="col-sm-6">
                                    <button class="btn btn-danger btn-lg btn-block py-3" style="height: 100px;" data-toggle="modal" data-target="#poweroff" <?php if($status == "offline") { echo "disabled"; } ?>>
                                    <i class="fas fa-power-off fa-2x"></i><br>
                                    <?php echo $lang['PowerOff']; ?>
                                    </button>
                                 </div>
                                 <div class="col-sm-6">
                                    <button class="btn btn-warning btn-lg btn-block py-3" style="height: 100px;" data-toggle="modal" data-target="#reboot" <?php if($status == "offline") { echo "disabled"; } ?>>
                                    <i class="fas fa-redo-alt fa-2x"></i><br>
                                    <?php echo $lang['Reboot']; ?>
                                    </button>
                                 </div>
                                 <?php } ?>
                              </div>
                              <div class="py-3">
                                 <ul class="list-group list-group-flush w-100 bg-light">
                                    <li class="list-group-item">
                                       <?php echo $lang['CPUUsage']; ?>: 
                                       <div class="progress" style="height: 25px">
                                          <div class="progress-bar" style="width:<?php echo round($cpu_usage); ?>%">
                                             <?php echo round($cpu_usage); ?>%
                                          </div>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <?php echo $lang['MemUsage']; ?>: 
                                       <div class="progress" style="height: 25px">
                                          <div class="progress-bar" style="width:<?php echo round($ram_usage, 2); ?>%">
                                             <?php echo round($ram_usage, 0); ?>%
                                          </div>
                                       </div>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                           <div class="col-sm-6">
                              <iframe 
                                 width="100%" 
                                 height="300" 
                                 frameborder="0" 
                                 scrolling="yes" 
                                 marginheight="0" 
                                 marginwidth="0" 
                                 src="https://maps.google.com/maps?q=<?php echo $lat; ?>,<?php echo $long; ?>&hl=hu&z=16&amp;output=embed"
                              >
                              </iframe>
                           </div>
                        </div>
                     </div>
                     <div class="tab-pane container" id="rootpwd">
                        <?php 
                           if($permission == 0)
                           {
                        ?>
                        <div class="alert alert-danger">
                           <?php echo $lang['NoPermission']; ?><br>
                              <a href="/dashboard" class="btn btn-light">
                                 <i class="fas fa-chevron-left"></i>
                                 <?php echo $lang['Back']; ?>
                              </a>
                        </div>
                        <?php
                           } 
                           else
                           {
                        ?>
                        <form method="POST" class="col-sm-6">
                           <div class="md-form md-bg">
                              <input type="password" name="rootpwd" class="form-control" id="rootpwd" maxlength="50">
                              <label for="rootpwd"><?php echo $lang['NewRootPwd']; ?></label>
                           </div>
                           <?php if(isset($emptyPwd)) { ?> 
                              <span class="text-danger">
                                 <i class="fas fa-exclamation-triangle"></i>
                                 <?php echo $emptyPwd; ?>
                              </span>
                           <?php } ?>
                           <div class="alert alert-info">
                              <?php echo $lang['PwdTips']; ?>
                           </div>
                           <div class="md-form md-bg">
                              <input type="password" name="pwdagain" class="form-control">
                              <label for="pwdagain"><?php echo $lang['RootPwdAgain']; ?></label>
                           </div>
                           <?php if(isset($emptyPwdAgain)) { ?> 
                           <span class="text-danger">
                              <i class="fas fa-exclamation-triangle"></i>
                              <?php echo $emptyPwdAgain; ?>
                           </span>
                           <?php } ?>
                           <button type="submit" name="btn-changepwd" class="btn btn-primary" <?php if($status == "offline") { echo "disabled"; } ?>>
                              <?php echo $lang['ChangePwd']; ?>
                           </button>
                        <?php } ?>
                     </div>
                     <div class="tab-pane container" id="hostname">
                     <?php 
                        if($permission == 0)
                        {
                        ?>
                     <div class="alert alert-danger">
                        <?php echo $lang['NoPermission']; ?><br>
                        <a href="/dashboard" class="btn btn-light">
                           <i class="fas fa-chevron-left"></i>
                           <?php echo $lang['Back']; ?>
                        </a>
                     </div>
                     <?php
                     } 
                     else
                     {
                     ?>
                     <form method="POST" class="col-sm-6">
                     <p class="lead">
                        <?php echo $lang['CurrentHostname']; ?>:
                        <?php echo $current_hostname; ?>
                     </p>
                     <div class="md-form md-bg col-sm-6" style="margin-top: 10px;">
                        <input type="text" name="hostname" class="form-control" maxlength="50">
                        <label for="hostname"><?php echo $lang['NewHostname']; ?></label>
                     </div>
                     <?php if(isset($emptyHostname)) { ?> 
                     <span class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $emptyHostname; ?>
                     </span><br>
                     <?php } ?>
                     <button type="submit" class="btn btn-primary" name="btn-changehn" <?php if($status == "offline") { echo "disabled"; } ?>> 
                        <?php echo $lang['ChangeHostname']; ?>
                     </button>
                     </form>
                     <?php } ?>
                     </div>
                     <div class="tab-pane container" id="delete">
                        <?php 
                           if($permission == 0)
                           {
                        ?>
                        <div class="alert alert-danger">
                           <?php echo $lang['NoPermission']; ?><br>
                           <a href="/dashboard" class="btn btn-light">
                           <i class="fas fa-chevron-left"></i>
                           <?php echo $lang['Back']; ?>
                           </a>
                        </div>
                        <?php
                           } 
                           else
                           {
                          ?>
                        <div class="alert alert-danger">
                           <div class="row">
                              <div class="col-sm-8">
                                 <?php echo $lang['DeleteServerText']; ?>
                              </div>
                              <div class="col-sm-4">
                                 <form method="POST">
                                    <button class="btn btn-block btn-danger" name="btn-delete">
                                    <?php echo $lang['DeleteServer']; ?>
                                    </button>
                                 </form>
                              </div>
                           </div>
                        </div>
                        <?php } ?>
                     </div>
                  </div>
                  <!-- Leállítás -->
                  <div class="modal" id="poweroff">
                     <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                           <div class="modal-header bg-light">
                              <h4 class="modal-title">
                                 <i class="fas fa-power-off"></i>
                                 <?php echo $lang['PowerOff']; ?>
                              </h4>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                           </div>
                           <div class="modal-body">
                              <span class="lead">
                              <?php echo $lang['SurePowerOff']; ?>
                              </span>
                           </div>
                           <div class="modal-footer">
                              <form method="POST">
                                 <button type="submit" name="btn-shutdown" class="btn btn-danger">
                                 <?php echo $lang['PowerOff']; ?>
                                 </button>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Újraindítás -->
               <div class="modal" id="reboot">
                  <div class="modal-dialog modal-lg">
                     <div class="modal-content">
                        <div class="modal-header bg-light">
                           <h4 class="modal-title">
                              <i class="fas fa-power-off"></i>
                              <?php echo $lang['Reboot']; ?>
                           </h4>
                           <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body lead py-3">
                           <?php echo $lang['SureRebootRemote']; ?>
                        </div>
                        <div class="modal-footer">
                           <form method="POST">
                              <button type="submit" class="btn btn-warning" name="btn-reboot">
                                 <?php echo $lang['Reboot']; ?>
                              </button>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
         //Space blokkolása  
         $("input#rootpwd").on({
            keydown: function(e) 
            {
            if (e.which === 32)
               return false;
            },
            change: function() 
            {
               this.value = this.value.replace(/\s/g, "");
            }
         });      
         $("input#hostname").on({
            keydown: function(e) 
            {
            if (e.which === 32)
                return false;
            },
            change: function() 
            {
                this.value = this.value.replace(/\s/g, "");
            }
         });  
         
         //Különleges karakterek blokkolása
         $('input').on('keypress', function (event) 
         {
            var regex = new RegExp("^[a-zA-Z0-9,.,#,=,?]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) 
            {
               event.preventDefault();
               return false;
            }
         });
      </script>
   </body>
</html>
<?php 
   $db->close(); 
   $remote->ssh_close();
?>