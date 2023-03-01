<?php
//Felhasználói fiók hozzáadása
    
session_start();
if(!isset($_SESSION['username']))
{
    header('Location: /login');
    exit();
}
    
require '../config/components.php'; 
$db = new DBconnect(); 
$username = $_SESSION['username'];
$account = $db->query('SELECT * FROM accounts WHERE username = ?', $_SESSION['username'])->fetchArray();

//Jelszó frissítése
$error = false;
if (isset($_POST['btn-upgrade']))
{
    $oldpwd = $_POST['oldpwd'];
    $oldpwd = strip_tags($oldpwd);    
    $oldpwd = htmlspecialchars($oldpwd);

    $newpwd = $_POST['newpwd'];
    $newpwd = strip_tags($newpwd);    
    $newpwd = htmlspecialchars($newpwd);

    if(empty($oldpwd))
    {
        $error = true;
        $errorOld = $lang['EmptyFields'];
    }

    //Jelszókvóta
    $pwd_requirement = '/^(?=.*[A-Z]).{9,}$/';
    if(!preg_match($pwd_requirement, $newpwd))
    {
        $error = true;
        $notStrenght = $lang['PwdStrength'];
    }

    if(!$error)
    {
        //Argon2ID titkosítás 
        $password_hash = password_hash($newpwd, PASSWORD_ARGON2ID, 
        [
            'memory_cost' => 2048, 
            'time_cost' => 4, 
            'threads' => 3
        ]);

        //Régi jelszó ellenőrzése
        $sql_count = $db->query("SELECT id FROM accounts WHERE username = '$username'");
        $count = $sql_count->numRows();
        if(password_verify($oldpwd, $account['password']))
        {
            //SQL update végrehajtása
            $db->query("UPDATE accounts SET password = '$password_hash' WHERE username = '$username'");

            //Naplózás
            $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
            array($_SESSION['username']))->fetchArray();
            $user_id = $log['id'];
            $user_ip = real_ip();
            $insert = $db->query('INSERT INTO account_logs (user_id, user_ip, action) VALUES (?,?,?)', 
            $user_id, $user_ip, "Jelszó frissítése");

            //Értesítés
            $change_success = $lang['ChangeSuccess'];

        }
        else
        {
            $pwdNotValid = $lang['PwdError'];
        }
    }
    
}

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['AddUser']; ?></title>
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
                  <h4 class="py-3 text-center">
                     <i class="fas fa-user"></i>
                     <?php echo $lang['Welcome']; 
                        echo htmlspecialchars($_SESSION['username']); ?>!
                  </h4>
                  <ul class="nav nav-tabs">
                     <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#accinfo">
                        <?php echo $lang['Details']; ?>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#passwd">
                           <?php echo $lang['ChangePwd']; ?>
                        </a>
                     </li>
                  </ul>
                  <div class="tab-content">
                     <div class="tab-pane container active" id="accinfo">
                        <div class="card" style="margin-top: 25px;">
                           <div class="card-body lead">
                              <b><i class="fas fa-envelope-open-text"></i> <?php echo $lang['EmailAddr']; ?>:</b> <?php echo htmlspecialchars($account['email']); ?><br>
                              <b><i class="fas fa-user"></i> <?php echo $lang['Username']; ?>:</b> <?php echo htmlspecialchars($account['username']); ?><br>
                              <b><i class="fas fa-calendar-check"></i> <?php echo $lang['AccCreated']; ?>:</b> <?php echo htmlspecialchars($account['account_created']); ?><br>
                           </div>
                        </div>
                        <div class="card" style="margin-top: 25px;">
                           <div class="card-body">
                              <h4 class="py-3">
                                 <i class="fas fa-list"></i>
                                 <?php echo $lang['LastLogins']; ?>
                              </h4>
                              <div class="table-responsive">
                                 <table class="table table-bordered table-stripped">
                                    <thead class="bg-dark text-white text-center">
                                       <tr>
                                          <th><?php echo $lang['IPAddr']; ?></th>
                                          <th><?php echo $lang['Date']; ?></th>
                                          <th><?php echo $lang['Device']; ?></th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <?php
                                          $db->query("SELECT * FROM account_logins LEFT JOIN accounts 
                                          ON accounts.id = account_logins.user_id WHERE accounts.username = '$username'
                                          ORDER BY account_logins.id DESC LIMIT 4")
                                          ->fetchAll(function($log) {
                                          ?>
                                       <tr class="lead text-center">
                                          <td>
                                             <?php echo htmlspecialchars($log['login_ipaddr']); ?>
                                          </td>
                                          <td>
                                             <?php echo htmlspecialchars($log['login_date']); ?>
                                          </td>
                                          <td>
                                             <?php
                                                $device_mobile = is_numeric(strpos(strtolower($log['login_device']), "mobile")); 
                                                $device_tablet = is_numeric(strpos(strtolower($log['login_device']), "tablet")); 
                                                $device_mswindows = is_numeric(strpos(strtolower($log['login_device']), "windows")); 
                                                $device_android = is_numeric(strpos(strtolower($log['login_device']), "android")); 
                                                $device_appleiphone = is_numeric(strpos(strtolower($log['login_device']), "iphone")); 
                                                $device_appleipad = is_numeric(strpos(strtolower($log['login_device']), "ipad")); 
                                                $device_appleiOS = $device_appleiphone || $device_appleipad; 
                                                 
                                                if($device_mobile)
                                                { 
                                                    if($device_tablet)
                                                    { 
                                                        echo '<i class="fas fa-tablet-alt"></i> '; 
                                                    }
                                                    else
                                                    { 
                                                        echo '<i class="fas fa-mobile-alt"></i> '; 
                                                    } 
                                                }
                                                else
                                                { 
                                                    echo '<i class="fas fa-desktop"></i> '; 
                                                } 
                                                 
                                                if($device_appleiOS)
                                                { 
                                                    echo ' iOS'; 
                                                }
                                                elseif($device_android)
                                                { 
                                                    echo ' Android'; 
                                                }
                                                elseif($device_mswindows)
                                                { 
                                                    echo ' Windows'; 
                                                }
                                                else
                                                {
                                                    echo " Linux/Unix";
                                                }
                                                ?>
                                          </td>
                                       </tr>
                                       <?php }); ?>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="tab-pane container fade" id="passwd">
                        <form method="POST" class="col-sm-6">
                           <?php if (isset($change_success)) { ?>
                           <div class="alert alert-success" style="margin-top: 25px;">
                              <i class="fas fa-check-circle"></i>
                              <?php echo $change_success; ?>
                           </div>
                           <?php } ?> 
                           <div class="md-form md-bg">
                              <input type="password" name="oldpwd" class="form-control">
                              <label for="oldpwd"><?php echo $lang['Password']; ?></label>
                              <?php if(isset($errorOld)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $errorOld; ?></span><?php } ?>
                              <?php if(isset($pwdNotValid)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $pwdNotValid; ?></span><?php } ?>
                           </div>
                           <div class="md-form md-bg">
                              <input type="password" name="newpwd" id="newpwd" class="form-control">
                              <label for="newpwd"><?php echo $lang['NewPwd']; ?></label>
                              <?php if(isset($notStrenght)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $notStrenght; ?></span><?php } ?>
                           </div>
                           <span class="btn btn-sm btn-cyan" onclick="showpwd()">
                           <i class="fas fa-eye"></i>
                           </span>
                           <div class="alert alert-info">
                              <?php echo $lang['PwdTips']; ?>
                           </div>
                           <button type="submit" name="btn-upgrade" class="btn btn-primary btn-block">
                                <?php echo $lang['ChangePwd']; ?>
                           </button>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
         function showpwd() 
         {
             var x = document.getElementById("newpwd");
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
         $("input#newpwd").on({
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