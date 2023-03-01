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
require 'securimage/securimage.php';

//Tűzfal aktiváció
$db = new DBconnect();
require '../includes/integrated_fw.php';
$db->close();

$error = false;
if(isset($_POST['btn-send']))
{
    //POST változók meghatározása
    $email = trim($_POST['email']);    
    $email = htmlspecialchars(strip_tags($email));

    $captcha_code = $_POST['captcha_code'];

      //Email cím validálása
      if(empty($email))
      {
        $error = true;
        $emailEmpty = $lang['FieldEmpty'];
      }
      elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
      {
        $error = true;
        $notvalidEmail = $lang['NotValidEmail'];
      }

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
        //Random string (token) generálása
        $lenght = 24;
        function generateToken($lenght)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $random_string = '';

            for ($i = 0; $i < $lenght; $i++) 
            {
                $index = rand(0, strlen($characters) - 1);
                $random_string .= $characters[$index];
            }          

            return $random_string;
        }

        //URL összerakása
        $token = generateToken($lenght);
        $localserver = $_SERVER['SERVER_ADDR'];
        $url = "http://{$localserver}/forgot-password/token/{$token}";

        //Fiókadatok
        $db = new DBconnect();
        $account = $db->query('SELECT * FROM accounts WHERE email = ?', $email)->fetchArray();
        $username = $account['username'];

        //Token frissítése
        $db->query("UPDATE accounts SET reset_token = '$token' WHERE email = '$email'");

        //Üzenet küldése
        $message = file_get_contents('../mail/pwdreset.html'); 
        $message = str_replace('{{username}}', $username, $message); 
        $message = str_replace('{{url}}', $url, $message); 
        $mail->addAddress($email);
        $mail->isHTML(true);    
        $mail->MsgHTML($message);                              
        $mail->Subject = 'Jelszó emlékeztető';
        $mail->send();

        $db->close();

        $sent = $lang['MailSent'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['ForgotPwd']; ?></title>
   </head>
   <body class="bg-light">
      <?php include '../includes/navbar.php'; ?>
      <div id="particles-js"></div>
      <div class="container h-100">
         <div class="row align-items-center h-100">
            <div class="col-sm-6 mx-auto">
               <div class="card shadow login-form">
                  <div class="card-header py-3 bg-primary text-white lead text-center">
                     <?php echo $lang['ForgotPwd']; ?>
                  </div>
                  <div class="card-body">
                     <?php if(isset($sent)) { ?>
                     <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $sent; ?>
                     </div>
                     <?php } ?>
                     <form method="POST">
                        <div class="md-form md-bg input-with-pre-icon">
                           <i class="fas fa-envelope-open-text input-prefix"></i>
                           <input type="email" name="email" class="form-control">
                           <label for="email"><?php echo $lang['EmailAddr']; ?></label>
                        </div>
                        <?php if(isset($emailEmpty)) { ?> 
                        <span class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $emailEmpty; ?>
                        </span>
                        <?php } 
                           if(isset($notvalidEmail)) { ?> 
                        <span class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $notvalidEmail; ?>
                        </span>
                        <?php } ?>
                        <div class="row">
                           <div class="col-sm-6">
                              <img id="captcha" src="securimage/securimage_show.php" style="margin-top: 25px;">
                              <a style="margin-top: 25px;" data-toggle="tooltip" data-placement="top"
                                 title="<?php echo $lang['NewCode']; ?>" class="btn btn-light btn-sm text-primary" href="#"
                                 onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false">
                              <i class="fas fa-sync"></i>
                              </a>
                           </div>
                           <div class="col-sm-6">
                              <div class="md-form md-bg input-with-pre-icon" style="margin-top: 50px;">
                                 <i class="fas fa-lock input-prefix"></i>
                                 <input type="text" name="captcha_code" class="form-control">
                                 <label for="captcha_code"><?php echo $lang['CaptchaCode']; ?></label>
                              </div>
                              <?php if(isset($codeEmpty)) { ?> 
                              <span class="text-danger">
                              <i class="fas fa-exclamation-triangle"></i>
                              <?php echo $codeEmpty; ?>
                              </span>
                              <?php } 
                                 if(isset($error_captcha)) { ?> 
                              <span class="text-danger">
                              <i class="fas fa-exclamation-triangle"></i> 
                              <?php echo $error_captcha; ?>
                              </span>
                              <?php } ?>
                           </div>
                        </div>
                        <div class="clearfix d-inline">
                           <a class="float-left py-3" href="/login"><i class="fas fa-chevron-left"></i> <?php echo $lang['Login']; ?></a>
                           <button type="submit" name="btn-send" <?php if(isset($_COOKIE['login_blocked']) && $_COOKIE['login_blocked'] == true) { echo "disabled"; } ?>
                              class="btn btn-primary shadow float-right"><?php echo $lang['SendMail']; ?></button>
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
         $(document).ready(function() 
         {
            $('[data-toggle="tooltip"]').tooltip();
         });
      </script>
   </body>
</html>