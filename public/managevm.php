<?php
   //KVM Virtuális gép kezelése
   
   session_start();
   if(!isset($_SESSION['username']))
   {
      header('Location: /login');
      exit();
   }
   
  require '../config/components.php';
  require 'securimage/securimage.php';
  require '../src/virtualization.class.php';
  $db = new DBconnect();
  $kvm = new KVM();

   $id = $_GET['id'];
   //Gép adatai
   $vm = $db->query('SELECT * FROM machines WHERE id = ?', 
   array($id))->fetchArray();
   $name = $vm['vm_name'];

   //Jogosultság ellenőrzés
   $account = $db->query('SELECT * FROM accounts LEFT JOIN permissions 
   ON accounts.id = permissions.user_id WHERE accounts.username = ?', 
   array($_SESSION['username']))->fetchArray();
   $permission = $account['role'];

   //Tűzfal aktiváció
   require '../includes/integrated_fw.php';

   //Virtuális szerver elindítása
   if(isset($_POST['btn-start']))
   {
      $kvm->startVM($name);

      //Esemény naplózása
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
      $user_id, $user_ip, "{$name} VM elindítása");

      //Értesítés
      $vm_started = $lang['VMStarted'];

   }

   //Virtuális szerver leállítása
   if(isset($_POST['btn-stop']))
   {
      $kvm->stopVM($name);
   
      //Esemény naplózása
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
      $user_id, $user_ip, "{$name} VM leállítása");
   
      //Értesítés
      $vm_stopped = $lang['VMStopped'];
   
   }

   //Virtuális szerver leállítása
   if(isset($_POST['btn-restart']))
   {
      $kvm->rebootVM($name);
      
      //Esemény naplózása
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
      $user_id, $user_ip, "{$name} VM újraindítása");
      
      //Értesítés
      $vm_restarted = $lang['VMRestarted'];
      
   }

   //Biztonsági mentés elkészítése
   $error = false;
   if(isset($_POST['btn-backup']))
   {

      $backup_name = htmlspecialchars($_POST['backup_name']);

      if(empty($backup_name))
      {
         $error = true;
         $backupEmpty = $lang['FieldEmpty'];
      }

      //Létező mentés vizsgálata
      $check = $db->query('SELECT * FROM machine_backups')->fetchAll();
      foreach ($check as $row) 
      {
        if($row['backup_name'] == $backup_name)
        {
            $error = true;
        }
      }  

      if (!$error)
      {

         $kvm->snapshotVM($name, $snapshot_name = $backup_name);

         $vm_id = $vm['id'];
         $db->query('INSERT INTO machine_backups (vm_id, backup_name) VALUES (?,?)', $vm_id, $backup_name);
   
         //Esemény naplózása
         $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
         array($_SESSION['username']))->fetchArray();
         $user_id = $log['id'];
         $user_ip = real_ip();
         $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
         $user_id, $user_ip, "{$name} VM biztonsági mentése");
   
         $backup_created = $lang['BackupSuccess'];

      }
   }

   //VM visszaállítása
   if(isset($_POST['btn-restore']))
   {
      $restore_name = htmlspecialchars($_POST['restore_name']);
      $backup_date = htmlspecialchars($_POST['backup_date']);

      //Esemény naplózása
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
      $user_id, $user_ip, "{$name} VM visszaállítása");

      $vm_restored = $lang['VMRestored'];
   }

   //VM mentésének törlése
   if(isset($_POST['btn-delete']))
   {
      $backup_name = htmlspecialchars($_POST['restore_name']);

      //Végrehajtás
      $kvm->delete_snapshot($name, $snapshot_name = $backup_name);
      $db->query("DELETE FROM machine_backups WHERE backup_name = '$backup_name'");

      //Esemény naplózása
      $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
      array($_SESSION['username']))->fetchArray();
      $user_id = $log['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
      $user_id, $user_ip, "{$name} VM mentésének törlése");

   }

   //VM bővítése
   if(isset($_POST['btn-upgrade']))
   {
      $memory = htmlspecialchars($_POST['memory']);

      if(empty($memory))
      {
         $error = true;
         $memEmpty = $lang['FieldEmpty'];
      }

      if(!$error)
      {
         //Frissítés
         $kvm->scaleVM($name, $memory);
         $db->query("UPDATE machines SET memory = '$memory' WHERE id = '$id'");

         //Esemény naplózása
         $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
         array($_SESSION['username']))->fetchArray();
         $user_id = $log['id'];
         $user_ip = real_ip();
         $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
         $user_id, $user_ip, "{$name} VM RAM bővítése");

         $vm_expanded = $lang['ScaleSuccess'];
      }
   }

   //VM törlése
   if(isset($_POST['btn-terminate']))
   {
      $captcha_code = $_POST['captcha_code'];
      //Captcha validáció
      $securimage = new Securimage();
      if(empty($captcha_code))
      {
        $error = true;
        $codeEmpty = $lang['FieldEmpty'];
      }
      elseif ($securimage->check($_POST['captcha_code']) == false) 
      {
        $error = true;
        $error_captcha = $lang['ErrorCaptcha'];
      }

     if(!$error)
     { 
         //Esemény naplózása
         $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
         array($_SESSION['username']))->fetchArray();
         $user_id = $log['id'];
         $user_ip = real_ip();
         $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
         $user_id, $user_ip, "{$name} VM törlése");

        $kvm->removeVM($name);
        $db->query("DELETE FROM machines WHERE id = '$id'");
        header('Location: /machines');
        exit();
     }

   }

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title>
         <?php echo $lang['ManageVM']; ?>
         (<?php echo $name; ?>)
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
                     <i class="fas fa-cloud"></i>
                     <?php 
                        echo htmlspecialchars($vm['vm_name']); 
                        $status = $kvm->statusVM($name);
                        if($status == "running")
                        {
                        ?>
                     <span class="badge badge-success">
                     <i class="fas fa-play"></i>
                     <?php echo $lang['Started']; ?>
                     </span> 
                     <?php
                        }
                        else 
                        {
                        ?>
                     <span class="badge badge-danger">
                     <i class="fas fa-stop"></i>
                     <?php echo $lang['Stopped']; ?>
                     </span>
                     <?php
                        }
                        ?>
                  </h3>
                  <ul class="nav nav-tabs" style="margin-top: 25px;">
                     <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#dash">
                        <?php echo $lang['Dashboard']; ?>
                        </a>
                     </li>
                     <?php if($permission == 1) { ?>
                     <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#backup">
                        <?php echo $lang['Backup']; ?>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#scale">
                        <?php echo $lang['MemoryExp']; ?>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#delete">
                        <?php echo $lang['DeleteServer']; ?>
                        </a>
                     </li>
                     <?php } ?>
                  </ul>
                  <?php if(isset($vm_started)) { ?>
                  <div class="alert alert-success" style="margin-top: 25px;">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $vm_started; ?>
                  </div>
                  <?php } 
                     if(isset($vm_stopped)) { ?>
                  <div class="alert alert-success" style="margin-top: 25px;">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $vm_stopped; ?>
                  </div>
                  <?php } 
                     if(isset($vm_restarted)) { ?>
                  <div class="alert alert-success" style="margin-top: 25px;">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $vm_restarted; ?>
                  </div>
                  <?php } 
                     if(isset($backup_created)) { ?>
                  <div class="alert alert-success" style="margin-top: 25px;">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $backup_created; echo $backup_name; ?>
                  </div>
                  <?php } 
                     if(isset($vm_restored)) { ?>
                  <div class="alert alert-success" style="margin-top: 25px;">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $vm_restored; echo $backup_date ?>
                  </div>
                  <?php } 
                     if(isset($vm_expanded)) { ?>
                  <div class="alert alert-success" style="margin-top: 25px;">
                     <i class="fas fa-check-circle"></i>
                     <?php echo $vm_expanded; ?>
                  </div>
                  <?php } ?>
                  <div class="tab-content">
                     <div class="tab-pane container active" id="dash">
                        <div class="py-3">
                           <?php echo $lang['OperatingSys']; ?>: <?php echo htmlspecialchars($vm['operating_system']); ?><br>
                           <?php echo $lang['Created']; ?>: <?php echo htmlspecialchars($vm['vm_created']); 
                              if ($permission == 1) {
                              ?>
                           <div class="card" style="margin-top: 25px;">
                              <div class="card-body">
                                 <form method="POST">
                                    <div class="row">
                                       <div class="col-sm-4">
                                          <button type="submit" class="btn btn-success btn-block" name="btn-start" <?php if($status == "running") { echo "disabled"; } ?>>
                                          <i class="fas fa-play fa-2x"></i><br>
                                          <?php echo $lang['StartVM']; ?>
                                          </button>
                                       </div>
                                       <div class="col-sm-4">
                                          <button type="submit" class="btn btn-danger btn-block" name="btn-stop" <?php if($status == "shut off") { echo "disabled"; } ?>>
                                          <i class="fas fa-stop fa-2x"></i><br>
                                          <?php echo $lang['StopVM']; ?>
                                          </button>
                                       </div>
                                       <div class="col-sm-4">
                                          <button type="submit" class="btn btn-warning btn-block" name="btn-restart" <?php if($status == "shut off") { echo "disabled"; } ?>>
                                          <i class="fas fa-redo fa-2x"></i><br>
                                          <?php echo $lang['RestartVM']; ?>
                                          </button>
                                       </div>
                                    </div>
                                 </form>
                              </div>
                           </div>
                           <?php } ?>
                        </div>
                     </div>
                     <div class="tab-pane container fade" id="backup">
                        <form method="POST" class="col-sm-6 py-3">
                           <div class="md-form md-bg">
                              <input type="text" name="backup_name" class="form-control" maxlength="50">
                              <label for="backup_name"><?php echo $lang['BackupName']; ?></label>
                              <?php if(isset($backupEmpty)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $backupEmpty; ?></span><?php } ?>
                           </div>
                           <button type="submit" name="btn-backup" class="btn btn-primary">
                           <?php echo $lang['CreateBackup']; ?>
                           </button>
                        </form>
                        <div class="table-responsive col-sm-8">
                           <table id="backups" name="backups" class="table table-bordered table-stripped">
                              <thead class="bg-dark text-white text-center">
                                 <tr>
                                    <th><?php echo $lang['BackupName']; ?></th>
                                    <th><?php echo $lang['Created']; ?></th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                    $db->query("SELECT * FROM machine_backups WHERE vm_id = '$id'")->fetchAll(function($backup) {
                                    ?>
                                 <tr class="lead text-center">
                                    <td>
                                       <?php echo htmlspecialchars($backup['backup_name']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($backup['backup_created']); ?>
                                    </td>
                                    <td>
                                       <form method="POST">
                                          <input type="hidden" name="restore_name" value="<?php echo htmlspecialchars($backup['backup_name']); ?>">
                                          <input type="hidden" name="backup_date" value="<?php echo htmlspecialchars($backup['backup_created']); ?>">
                                          <button type="submit" name="btn-restore" class="btn btn-sm btn-warning">
                                          <i class="fas fa-redo-alt"></i>
                                          </button>
                                          <button type="submit" name="btn-delete" class="btn btn-sm btn-danger">
                                             <i class="fas fa-trash-alt"></i>
                                          </button>
                                       </form>
                                    </td>
                                 </tr>
                                 <?php }); ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="tab-pane container fade" id="scale">
                        <form method="POST" class="col-sm-6">
                           <p class="lead py-3">
                              <?php echo $lang['CurrentMemory']; echo htmlspecialchars($vm['memory']); ?> MB
                           </p>
                           <div class="md-form md-bg">
                              <input type="number" name="memory" class="form-control" maxlength="6" min="128" step="128" minlegth="3"> 
                              <label for="memory"><?php echo $lang['Memory']; ?> (MB)</label>
                              <?php if(isset($memEmpty)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $memEmpty; ?></span><?php } ?>
                           </div>
                           <button type="submit" name="btn-upgrade" class="btn btn-primary">
                              <?php echo $lang['Save']; ?>
                           </button>
                        </form>
                     </div>
                     <div class="tab-pane container fade" id="delete">
                        <form method="POST" class="py-3">
                           <div class="row">
                              <div class="col-sm-4">
                                 <img id="captcha" src="/securimage/securimage_show.php" style="margin-top: 25px;">
                                 <a style="margin-top: 25px;" data-toggle="tooltip" data-placement="top"
                                    title="<?php echo $lang['NewCode']; ?>" class="btn btn-light btn-sm text-primary" href="#"
                                    onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">
                                    <i class="fas fa-sync"></i>
                                 </a>
                              </div>
                              <div class="col-sm-4">
                                 <div class="md-form md-bg input-with-pre-icon" style="margin-top: 50px;">
                                    <i class="fas fa-lock input-prefix"></i>
                                    <input type="text" name="captcha_code" class="form-control">
                                    <label for="captcha_code"><?php echo $lang['CaptchaCode']; ?></label>
                                    <?php if(isset($codeEmpty)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $codeEmpty; ?></span><?php } ?>
                                    <?php if(isset($error_captcha)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $error_captcha; ?></span><?php } ?>
                                 </div>
                                 <p class="lead"><?php echo $lang['DeleteVM']; ?></p>
                                 <button class="btn btn-block btn-danger" name="btn-terminate">
                                    <?php echo $lang['DeleteServer']; ?>
                                 </button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
         //Space blokkolása  
         $("input#backup_name").on({
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
             var regex = new RegExp("^[a-zA-Z0-9,.]+$");
             var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
             if (!regex.test(key)) 
             {
                 event.preventDefault();
                 return false;
             }
         });
         
         $(document).ready(function () 
          {
             $('#backups').DataTable();
          });
      </script>
   </body>
</html>
<?php $db->close(); ?>