<?php

//Bejelentkezés

session_start();
//Átirányítás, ha be van jelentkezve a felhasználó
if(isset($_SESSION['username']))
{
    header('Location: /dashboard');
}

require '../config/components.php';

//Tor böngésző blokkolása
$waf = new WebAppFirewall();
$waf->anti_tor();
require 'securimage/securimage.php';

//Tűzfal aktiváció
$db = new DBconnect();
require '../includes/integrated_fw.php';
$db->close();

$_SESSION['logincount'];

$error = false;
if(isset($_POST['btn-login']))
{

    //POST változók meghatározása
    $email = trim($_POST['email']);    
    $email = htmlspecialchars(strip_tags($email));

    $password = trim($_POST['password']);
    $password = htmlspecialchars(strip_tags($password));

    $captcha_code = $_POST['captcha_code'];

    //Jelszó mező vizsgálata
    if(empty($password))
    {
        $error = true;
        $pwdEmpty = $lang['FieldEmpty'];
    }

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

    //Adatok ellenőrzése adatbázisból
    if(!$error)
    {
        //Adatbázis kapcsolat
        $db = new DBconnect();

        //Jelszó ellenőrzése és sorok megszámlálása
        $account = $db->query('SELECT * FROM accounts WHERE email = ?', array($email))->fetchArray();
        $sql_count = $db->query("SELECT id FROM accounts WHERE email = '$email'");
        $count = $sql_count->numRows();

        if($count == 1 && password_verify($password, $account['password']))
        {

            //Session beállítása
            $_SESSION['username'] = $account['username'];

            //Ha sikerül a bejelentkezés, az elrontott bejelentkezések száma nullázódik
            $_SESSION['logincount'] == 0;

            //Bejelentkezés naplózása
            $user_id = $account['id'];
            $login_ipaddr = real_ip();
            $login_device = $_SERVER['HTTP_USER_AGENT'];
            $db->query('INSERT INTO account_logins (user_id, login_ipaddr, login_device) VALUES (?,?,?)', $user_id, $login_ipaddr, $login_device);

            //Átirányítás
            $login_success = $lang['LoginSuccess'];
            header('refresh:3; url=/dashboard');

            //Adatbázis kapcsolat bezárása
            $db->close(); 

        }
        else
        {
            //Hiba kiiratása
            $login_error = $lang['ErrorLogin'];

            //Számolja a hibás belépéseket
            $_SESSION['logincount']++;

            //Hátralévő bejelentkezések kiiratása a látogatónak
            $lcremaining = 5 - $_SESSION['logincount'];
            $lcremaining_warn = $lang['LcRemaining'];

            //Látogató kitiltása 6 elrontott kísérlet után 10 percre
            if ($_SESSION['logincount'] > 5)
            {
                header('');
                setcookie("login_blocked", "10_minutes", time() + (60 * 10));
                $login_blocked = $lang['LoginBlocked'];
            }
        }
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/header.html'; ?>
    <title><?php echo $lang['Login']; ?></title>
</head>
<body class="bg-light">
    <?php include '../includes/navbar.php'; ?>
    <div id="particles-js"></div> 
    <div class="container h-100">
        <div class="row align-items-center h-100">
            <div class="col-sm-6 mx-auto">
                <div class="card shadow login-form">
                    <div class="card-header py-3 bg-primary text-white lead text-center">
                        <?php echo $lang['Login']; ?>
                    </div>
                    <div class="card-body">
                        <?php
                            if (isset($login_error)) { ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-times"></i>
                                    <?php echo $login_error; ?>
                                </div>
                           <?php } 
                           if (isset($lcremaining_warn)) { ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo $lcremaining_warn; echo $lcremaining; ?> 
                            </div>
                            <?php } 
                            if(isset($_COOKIE['login_blocked']) OR $_COOKIE['login_blocked'] == true) { ?> 
                                <div class="alert alert-danger">
                                    <i class="fas fa-times"></i>
                                    <?php echo $lang['LoginBlocked']; ?>
                                </div>
                           <?php } 
                            if (isset($login_success)) { ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo $login_success; ?><br>
                                    <?php echo $lang['Welcome']; echo htmlspecialchars($_SESSION['username']); ?><br>
                                    <i class="fa fa-spinner fa-spin"></i> <?php echo $lang['Redirecting']; ?>
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
                        <div class="md-form md-bg input-with-pre-icon">
                            <i class="fas fa-key input-prefix"></i>
                            <input type="password" name="password" class="form-control">
                            <label for="password"><?php echo $lang['Password']; ?></label>
                        </div>
                        <?php if(isset($pwdEmpty)) { ?> 
                            <span class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <?php echo $pwdEmpty; ?>
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
                            <a class="float-left py-3" href="/forgot-password"><?php echo $lang['ForgotPwd']; ?></a>
                            <button type="submit" name="btn-login" <?php if(isset($_COOKIE['login_blocked']) && $_COOKIE['login_blocked'] == true) { echo "disabled"; } ?>
                            class="btn btn-primary shadow float-right"><?php echo $lang['Login']; ?></button>
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