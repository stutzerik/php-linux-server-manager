<?php

//Root jelszó változtatás

session_start();
if(!isset($_SESSION['username']))
{
    header('Location: /login');
    exit();
}

require '../config/components.php';
require '../src/system.class.php';

$db = new DBconnect();
$system = new System();

//Tűzfal aktiváció
require '../includes/integrated_fw.php';

$error = false;
if(isset($_POST['btn-changepwd']))
{
    $rootpwd = trim($_POST['rootpwd']);    
    $rootpwd = htmlspecialchars(strip_tags($rootpwd));

    $pwdagain = trim($_POST['pwdagain']);    
    $pwdagain = htmlspecialchars(strip_tags($pwdagain));

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

    $pwd_requirement = '/^(?=.*[A-Z]).{9,}$/';
    if(!preg_match($pwd_requirement, $rootpwd))
    {
        $error = true;
        $notStrenght = $lang['PwdStrength'];
    }

    if($rootpwd !== $pwdagain)
    {
        $error = true;
        $notMatch = $lang['PwdNotMatch'];
    }

    if(!$error)
    {
        $system->change_root_pwd($rootpwd);

        $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
        array($_SESSION['username']))->fetchArray();
        $id = $log['id'];
        $user_ip = real_ip();
        $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
        $id, $user_ip, "Root jelszó megváltoztatása");

        $pwdchanged = $lang['RootPwdChanged'];

    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include '../includes/header.html'; ?>
  <title><?php echo $lang['RootPwd']; ?></title>
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
                      <i class="fas fa-key"></i> 
                      <?php echo $lang['RootPwd']; ?>
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
                            <?php if (isset($notStrenght))
                            { ?>
                                <div class="alert alert-danger">
                                    <?php echo $notStrenght; ?>
                                </div>
                            <?php } if(isset($notMatch)) { ?>
                                <div class="alert alert-danger">
                                    <?php echo $notMatch; ?>
                                </div>
                            <?php } if(isset($pwdchanged))
                            { ?> 
                               <div class="alert alert-success">
                                <?php echo $pwdchanged; ?>
                               </div>
                            <?php } ?>
                            <div class="md-form md-bg">
                                <input type="password" name="rootpwd" class="form-control" id="rootpwd" maxlength="30">
                                <label for="rootpwd"><?php echo $lang['NewRootPwd']; ?></label>
                            </div>
                            <?php if(isset($emptyPwd)) { ?> 
                                <span class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo $emptyPwd; ?>
                                </span>
                            <?php } ?>
                            <br><span class="btn btn-sm btn-cyan" onclick="showpwd()">
                                <i class="fas fa-eye"></i>
                            </span><br>
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
                            <button type="submit" class="btn btn-primary" name="btn-changepwd">
                                <?php echo $lang['ChangePwd']; ?>
                            </button>
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
<?php $db->close(); ?>