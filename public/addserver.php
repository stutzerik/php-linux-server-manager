<?php

//Szerver hozzáadása

session_start();
if(!isset($_SESSION['username']))
{
   header('Location: /login');
   exit();
}

require '../config/components.php';
$db = new DBConnect();

//Tűzfal aktiváció
require '../includes/integrated_fw.php';

$error = false;
if(isset($_POST['btn-connect']))
{
   //Változók
   $region = trim($_POST['region']);
   $region = htmlspecialchars(strip_tags($region));

   $flag = trim($_POST['flag']);
   $flag = htmlspecialchars(strip_tags($flag));

   $ip_address = trim($_POST['ip_address']);
   $ip_address = htmlspecialchars(strip_tags($ip_address));

   $ssh_port = trim($_POST['ssh_port']);
   $ssh_port = htmlspecialchars(strip_tags($ssh_port));

   $rootpwd = trim($_POST['rootpwd']);
   $rootpwd = htmlspecialchars(strip_tags($rootpwd));

   $server_id = rand(100000, 999999);

   if(empty($region) OR empty($flag) OR empty($ip_address) OR empty($ssh_port) OR empty($rootpwd))
   {
      $error = true;
      $emptyFields = $lang['EmptyFields'];
   }

   //Localhost nem adható hozzá a távoli szerverek csoportjához
   if($ip_address == "127.0.0.1" OR $ip_address == "localhost")
   {
      $error = true;
      $noLocalServer = $lang['NoLocalServer'];
   }

   //A szerver azonosítója nem ismétlődhet
   //mert így két azonos kukcs keletkezik, ami redundanciához vezet.
   $serverid = $db->query('SELECT server_id FROM remote_servers')->fetchAll();
   foreach ($serverid as $keyid) 
   {
      if($keyid['server_id'] == $server_id)
      {
         $error = true;
         $systemError = $lang['SystemError'];
      }
   }

   //Szerverismétlődés kezelése
   $repeat_ip = $db->query('SELECT ip_address FROM remote_servers')->fetchAll();
   foreach ($repeat_ip as $ip) 
   {
      if($ip['ip_address'] == $ip_address)
      {
         $error = true;
         $noIPrepeat = $lang['NoIPRepeat'];
      }
   }

   if(!$error)
   {
      //SQL insertálás
      $db->query('INSERT INTO remote_servers (region, flag, ip_address, ssh_port, server_id) VALUES (?,?,?,?,?)', 
      $region, $flag, $ip_address, $ssh_port, $server_id);

      //Root jelszó kulcssá formálása
      $rootpwd = base64_encode($rootpwd);
      shell_exec("sudo sh -c 'echo {$rootpwd} >> /var/www/ssh_keys/{$server_id}.key'");

      $hostAdded = $lang['HostAdded'];
   }

}

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Addserver']; ?></title>
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
                    <i class="fas fa-plug"></i>
                    <?php echo $lang['Addserver']; ?>
                 </h3>
                  <?php 
                     //Jogosultság ellenőrzés
                     $account = $db->query('SELECT * FROM accounts LEFT JOIN permissions 
                     ON accounts.id = permissions.user_id WHERE accounts.username = ?', 
                     array($_SESSION['username']))->fetchArray();
                     $permission = $account['role'];
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
                        <?php if(isset($emptyFields)) { ?>
                           <div class="alert alert-danger py-3">
                              <i class="fas fa-times"></i>
                              <?php echo $emptyFields; ?>
                           </div>
                        <?php }  
                        if(isset($noLocalServer)) { ?>
                           <div class="alert alert-danger py-3">
                              <i class="fas fa-times"></i>
                              <?php echo $noLocalServer; ?>
                           </div>
                        <?php } 
                        if(isset($systemError)) { ?>
                           <div class="alert alert-warning py-3">
                              <i class="fas fa-times"></i>
                              <?php echo $systemError; ?>
                           </div>
                        <?php } 
                        if(isset($hostAdded)) { ?>
                           <div class="alert alert-success py-3">
                              <i class="fas fa-check-circle"></i>
                              <?php echo $hostAdded; ?>
                           </div>
                        <?php } 
                        if(isset($noIPrepeat)) { ?>
                           <div class="alert alert-danger py-3">
                              <i class="fas fa-times"></i>
                              <?php echo $noIPrepeat; ?>
                           </div>
                        <?php } ?>
                        <p><?php echo $lang['OnlyLinux']; ?></p>
                        <div class="md-form md-bg">
                           <input type="text" name="region" class="form-control" maxlength="50">
                           <label for="region"><?php echo $lang['Region']; ?></label>
                        </div>
                        <label><?php echo $lang['Country']; ?></label>
                        <select id="flag" name="flag" class="form-control">
                        <?php 
                           if($_SESSION['lang'] == "hu") { 
                        ?>
                           <option value="af">Afganisztán</option>
                           <option value="al">Albánia</option>
                           <option value="dz">Algéria</option>
                           <option value="ad">Andorra</option>
                           <option value="aw">Aruba</option>
                           <option value="au">Ausztrália</option>
                           <option value="at">Ausztria</option>
                           <option value="az">Azerbajdzsán</option>
                           <option value="bd">Bangladesh</option>
                           <option value="by">Belarusz</option>
                           <option value="be">Belgium</option>
                           <option value="bm">Bermuda</option>
                           <option value="bo">Bolívia</option>
                           <option value="ba">Bosznia-Hercegovina</option>
                           <option value="br">Brazília</option>
                           <option value="bg">Bulgária</option>
                           <option value="cl">Chile</option>
                           <option value="ca">Kanada</option>
                           <option value="cn">Kína</option>
                           <option value="co">Kolumbia</option>
                           <option value="cg">Kongó</option>
                           <option value="cd">Kongó Demokratikus Köztársaság</option>
                           <option value="hr">Horvátország</option>
                           <option value="cu">Kuba</option>
                           <option value="cy">Ciprus</option>
                           <option value="cz">Csehország</option>
                           <option value="dk">Dánia</option>
                           <option value="do">Dominikai Köztársaság</option>
                           <option value="eg">Egyiptom</option>
                           <option value="sv">El Salvador</option>
                           <option value="et">Etiópia</option>
                           <option value="fi">Finnország</option>
                           <option value="fr">Franciaország</option>
                           <option value="de">Németország</option>
                           <option value="gi">Gibraltár</option>
                           <option value="gr">Görögország</option>
                           <option value="gl">Grönland</option>
                           <option value="ht">Haiti</option>
                           <option value="hk">Hong Kong</option>
                           <option value="hu">Magyarország</option>
                           <option value="in">India</option>
                           <option value="id">Indonézia</option>
                           <option value="iq">Irak</option>
                           <option value="il">Izrael</option>
                           <option value="it">Olaszország</option>
                           <option value="jm">Jamaika</option>
                           <option value="jp">Japán</option>
                           <option value="kz">Kazahsztán</option>
                           <option value="ke">Kenya</option>
                           <option value="ki">Kiribati</option>
                           <option value="kp">Korea Demokratikus Köztársaság</option>
                           <option value="kr">Korea Köztársaság</option>
                           <option value="kw">Kuwait</option>
                           <option value="kg">Kirgizisztán</option>
                           <option value="lb">Libanon</option>
                           <option value="lu">Luxemburg</option>
                           <option value="mg">Madagaszkár</option>
                           <option value="mt">Málta</option>
                           <option value="mx">Mexikó</option>
                           <option value="md">Moldovai Köztársaság</option>
                           <option value="me">Montenegró</option>
                           <option value="np">Nepál</option>
                           <option value="nl">Hollandia</option>
                           <option value="an">Holland Antillák</option>
                           <option value="nz">Új Zéland</option>
                           <option value="ng">Nigéria</option>
                           <option value="no">Norvégia</option>
                           <option value="pk">Pakisztán</option>
                           <option value="pw">Palau</option>
                           <option value="pa">Panama</option>
                           <option value="pe">Peru</option>
                           <option value="pl">Lengyelország</option>
                           <option value="pt">Portugália</option>
                           <option value="ro">Románia</option>
                           <option value="ru">Oroszország</option>
                           <option value="rs">Szerbia</option>
                           <option value="sg">Szingapúr</option>
                           <option value="sk">Szlovákia</option>
                           <option value="si">Szlovénia</option>
                           <option value="za">Dél-Afrika</option>
                           <option value="es">Spanyolország</option>
                           <option value="se">Svédország</option>
                           <option value="ch">Svájc</option>
                           <option value="tw">Taiwan</option>
                           <option value="th">Thaiföld</option>
                           <option value="ug">Uganda</option>
                           <option value="ua">Ukrajna</option>
                           <option value="ae">Egyesült Arab Emírségek</option>
                           <option value="gb">Egyesült Királyság</option>
                           <option value="us">Egyesült Államok</option>
                           <option value="vn">Vietnám</option>
                        <?php } else { ?>
                           <option value="af">Afganistan</option>
                           <option value="al">Albania</option>
                           <option value="dz">Algeria</option>
                           <option value="ad">Andorra</option>
                           <option value="aw">Aruba</option>
                           <option value="au">Australia</option>
                           <option value="at">Austria</option>
                           <option value="az">Azerbaijan</option>
                           <option value="bd">Bangladesh</option>
                           <option value="by">Belarusz</option>
                           <option value="be">Belgium</option>
                           <option value="bm">Bermuda</option>
                           <option value="bo">Bolivia</option>
                           <option value="ba">Bosnia and Herzegovina</option>
                           <option value="br">Brazil</option>
                           <option value="bg">Bulgaria</option>
                           <option value="cl">Chile</option>
                           <option value="ca">Canada</option>
                           <option value="cn">China</option>
                           <option value="co">Colombia</option>
                           <option value="cg">Congo</option>
                           <option value="cd">Democratic Republic of the Congo</option>
                           <option value="hr">Croatia</option>
                           <option value="cu">Cuba</option>
                           <option value="cy">Cyprus</option>
                           <option value="cz">Czech Republic</option>
                           <option value="dk">Denmark</option>
                           <option value="do">Dominican Republic</option>
                           <option value="eg">Egypt</option>
                           <option value="sv">El Salvador</option>
                           <option value="et">Ethiopia</option>
                           <option value="fi">Finland</option>
                           <option value="fr">France</option>
                           <option value="de">Germany</option>
                           <option value="gi">Gibraltar</option>
                           <option value="gr">Greece</option>
                           <option value="gl">Greenland</option>
                           <option value="ht">Haiti</option>
                           <option value="hk">Hong Kong</option>
                           <option value="hu">Hungary</option>
                           <option value="in">India</option>
                           <option value="id">Indonesia</option>
                           <option value="iq">Iraq</option>
                           <option value="il">Israel</option>
                           <option value="it">Italy</option>
                           <option value="jm">Jamaica</option>
                           <option value="jp">Japan</option>
                           <option value="kz">Kazakhstan</option>
                           <option value="ke">Kenya</option>
                           <option value="ki">Kiribati</option>
                           <option value="kp">Democratic Republic of Korea</option>
                           <option value="kr">Republic of Korea</option>
                           <option value="kw">Kuwait</option>
                           <option value="kg">Kyrgyzstan</option>
                           <option value="lb">Lebanon</option>
                           <option value="lu">Luxembourg</option>
                           <option value="mg">Madagascar</option>
                           <option value="mt">Malta</option>
                           <option value="mx">Mexico</option>
                           <option value="md">Republic of Moldova</option>
                           <option value="me">Montenegro</option>
                           <option value="np">Nepal</option>
                           <option value="nl">Netherlands</option>
                           <option value="an">Netherlands - Antilles</option>
                           <option value="nz">New Zealand</option>
                           <option value="ng">Nigeria</option>
                           <option value="no">Norway</option>
                           <option value="pk">Pakistan</option>
                           <option value="pw">Palau</option>
                           <option value="pa">Panama</option>
                           <option value="pe">Peru</option>
                           <option value="pl">Poland</option>
                           <option value="pt">Portugal</option>
                           <option value="ro">Romania</option>
                           <option value="ru">Russia</option>
                           <option value="rs">Serbia</option>
                           <option value="sg">Singapore</option>
                           <option value="sk">Slovakia</option>
                           <option value="si">Slovenia</option>
                           <option value="za">South Africa</option>
                           <option value="es">Spain</option>
                           <option value="se">Sweden</option>
                           <option value="ch">Switzerland</option>
                           <option value="tw">Taiwan</option>
                           <option value="th">Thailand</option>
                           <option value="ug">Uganda</option>
                           <option value="ua">Ukraine</option>
                           <option value="ae">United Arab Emirates</option>
                           <option value="gb">United Kingdom</option>
                           <option value="us">United States</option>
                           <option value="vn">Vietnam</option>
                        <?php } ?>
                        </select>
                        <div class="md-form md-bg">
                           <input type="text" name="ip_address" class="form-control" maxlength="32">
                           <label for="ip_address"> <?php echo $lang['IPAddr']; ?></label>
                        </div>
                        <div class="md-form md-bg">
                           <input type="number" name="ssh_port" class="form-control" maxlength="4">
                           <label for="ssh_port"> SSH Port</label>
                        </div>
                        <div class="md-form md-bg">
                           <input type="password" name="rootpwd" class="form-control" maxlength="50" id="rootpwd">
                           <label for="rootpwd"> <?php echo $lang['RootPwd']; ?></label>
                           <span class="btn btn-sm btn-cyan" onclick="showpwd()">
                              <i class="fas fa-eye"></i>
                           </span>
                        </div>
                        <label class="form-text text-muted" style="font-size: 14px !important;"><?php echo $lang['PwdChars']; ?></label>
                        <div class="md-form md-bg">
                           <button type="submit" name="btn-connect" class="btn btn-block btn-primary shadow">
                              <?php echo $lang['Addserver']; ?>
                           </button>
                        </div>
                     </form>
                     <?php
                     }
                     ?>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
        function showpwd() 
        {
            var x = document.getElementById("rootpwd");
            if (x.type === "password") 
            {
                x.type = "text";
            } 
            else 
            {
                x.type = "password";
            }
        } 
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
    </script>
   </body>
</html>
<?php 
//DB kapcsolat lezárása
$db->close();
?>