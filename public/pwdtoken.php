<?php

//Elfelejtett jelszó

session_start();
//Átirányítás, ha be van jelentkezve a felhasználó
if(isset($_SESSION['username']))
{
   header('Location: /dashboard');
}

require '../config/components.php';
require '../config/mailconfig.php';

//Tor böngésző blokkolása
$waf = new WebAppFirewall();
$waf->anti_tor();

//Token azonosítása
$token = $_GET['token'];

//Fiókadatok
$db = new DBconnect();
$account = $db->query('SELECT * FROM accounts WHERE reset_token = ?', $token)->fetchArray();
$username = $account['username'];
$email = $account['email'];
$validate = $db->query("SELECT * FROM accounts WHERE reset_token = '$token'");
$count = $validate->numRows();

//Tűzfal aktiváció
require '../includes/integrated_fw.php';

$error = false;
if(isset($_POST['btn-change']))
{
   $password = trim($_POST['password']);    
   $password = htmlspecialchars(strip_tags($password));

   if(empty($password))
   {
      $error = true;
      $errorPassword = $lang['FieldEmpty'];
   }

   //Jelszókvóta
   $pwd_requirement = '/^(?=.*[A-Z]).{9,}$/';
   if(!preg_match($pwd_requirement, $password))
   {
      $error = true;
      $notStrenght = $lang['PwdStrength'];
   }

   if(!$error)
   {

      //Argon2ID titkosítás 
      $password_hash = password_hash($password, PASSWORD_ARGON2ID, 
      [
         'memory_cost' => 2048, 
         'time_cost' => 4, 
         'threads' => 3
      ]);

      //Jelszó frissítése
      $db->query("UPDATE accounts SET password = '$password_hash' WHERE reset_token = '$token'");

      //Naplózás
      $user_id = $account['id'];
      $user_ip = real_ip();
      $insert = $db->query('INSERT INTO account_logs (user_id, user_ip, action) VALUES (?,?,?)', 
      $user_id, $user_ip, "Elfelejtett jelszó frissítése");

      //Üzenet küldése
      $message = file_get_contents('../mail/pwdchanged.html'); 
      $message = str_replace('{{username}}', $username, $message); 
      $message = str_replace('{{password}}', $password, $message); 
      $mail->addAddress($email);
      $mail->isHTML(true);    
      $mail->MsgHTML($message);                              
      $mail->Subject = 'Jelszó megváltoztatva';
      $mail->send();

      //Token frissítése
      $db->query("UPDATE accounts SET reset_token = NULL WHERE email = '$email'");

      //Értesítés
      $change_success = $lang['ChangeSuccess'];
      header('refresh:3; url=/login');

   }
}

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['ChangePwd']; ?></title>
   </head>
   <body class="bg-light">
      <?php include '../includes/navbar.php'; ?>
      <div id="particles-js"></div>
      <div class="container h-100">
         <div class="row align-items-center h-100">
            <div class="col-sm-6 mx-auto">
               <div class="card shadow login-form">
                  <div class="card-header py-3 bg-primary text-white lead text-center">
                     <?php echo $lang['ChangePwd']; ?>
                  </div>
                  <div class="card-body">
                     <?php if (isset($change_success)) { ?>
                     <div class="alert alert-success" style="margin-top: 25px;">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $change_success; ?><br>
                        <i class="fa fa-spinner fa-spin"></i> <?php echo $lang['Redirecting']; ?>
                     </div>
                     <?php } ?> 
                     <form method="POST">
                        <?php if(($count == 0) OR ($count > 1)) { ?>
                        <div class="alert alert-danger">
                           <i class="fas fa-exclamation-triangle"></i>
                           <?php echo $lang['InvalidCode']; ?>
                        </div>
                        <?php } else { ?>
                        <h4 class="text-center">
                           <?php 
                              echo $lang['Welcome'];
                              echo htmlspecialchars($account['username']);
                              ?>!
                        </h4>
                        <div class="md-form md-bg">
                           <input type="password" name="password" id="password" class="form-control" maxlength="150">
                           <label for="password"><?php echo $lang['NewPwd']; ?></label>
                           <?php if(isset($errorPassword)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo $errorPassword; ?></span><?php } ?>
                           <?php if(isset($notStrenght)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo $notStrenght; ?></span><?php } ?>
                        </div>
                        <span class="btn btn-sm btn-cyan" onclick="showpwd()">
                        <i class="fas fa-eye"></i>
                        </span>
                        <div class="alert alert-info">
                           <?php echo $lang['PwdTips']; ?>
                        </div>
                        <?php } ?>
                        <div class="clearfix d-inline">
                           <a class="float-left py-3" href="/login"><i class="fas fa-chevron-left"></i> <?php echo $lang['Login']; ?></a>
                           <button type="submit" name="btn-change" <?php if(isset($_COOKIE['login_blocked']) && $_COOKIE['login_blocked'] == true) { echo "disabled"; } if(($count == 0) OR ($count > 1)) { echo "disabled"; } ?>
                              class="btn btn-primary shadow float-right"><?php echo $lang['ChangePwd']; ?></button>
                        </div>
                     </form>
                  </div>
                  <div class="card-footer text-center py-3">
                     <i class="fas fa-fingerprint text-primary"></i> 
                     <?php echo $lang['IPLogging']; ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script src="/theme/particlesjs/particles.min.js"></script>
      <script src="/theme/particlesjs/config.min.js"></script>
      <script>
         function showpwd() 
         {
             var x = document.getElementById("password");
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
         $("input#password").on({
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
<?php $db->close(); ?>