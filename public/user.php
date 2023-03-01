<?php
//Felhasználói fiók
    
session_start();
if(!isset($_SESSION['username']))
{
    header('Location: /login');
    exit();
}
    
require '../config/components.php'; 
$db = new DBconnect(); 

//Azonosítás ID alapján
$id = $_GET['id']; 

//Saját fiók azonosítása
//Lekéri a saját fiók információit, ha megegyezik az ID-ban talált adatokkal
//letiltja a fiók törlését - a saját fiókját ne törölhesse az admin
$username = $_SESSION['username'];
$current_account = $db->query('SELECT id FROM accounts WHERE username = ?', 
array($_SESSION['username']))->fetchArray();

//Email és jogosultság adatlapjának frissítése.
$error = false;
if(isset($_POST['btn-update']))
{
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    if(empty($email))
    {
        $error = true;
        $msgEmpty = $lang['EmptyFields'];
    }

    if(!$error)
    {
        //Táblák frissítése LEFT JOIN-al
        $db->query("UPDATE permissions LEFT JOIN accounts 
        ON permissions.user_id = accounts.id
        SET accounts.email = '$email', permissions.role = '$role'
        WHERE permissions.user_id = '$id' AND accounts.id = '$id'");

        //Naplózás
        $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
        array($_SESSION['username']))->fetchArray();
        $user_ip = real_ip();
        $insert = $db->query('INSERT INTO account_logs (user_id, user_ip, action) VALUES (?,?,?)', 
        $id, $user_ip, "Email és jogosultság szerkesztése ({$username} által)");

        //Értesítés a sikeres frissítésről
        $updated = $lang['ProfileUpdated'];
    }
}

//Felhasználói fiók törlése ID alapján
if(isset($_POST['btn-delete']))
{
    //Törlés az "accounts" táblából és a "permissionsból"
    $db->query("DELETE FROM accounts WHERE id = '$id'");
    $db->query("DELETE FROM permissions WHERE user_id = '$id'");

    //Átirányítás, majd kilépés
    header('Location: /accounts');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Users']; ?> (<?php echo $id; ?>)</title>
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
                    //Felhasználó email címének és jogosultságának frissítése
                     $details = $db->query("SELECT * FROM accounts LEFT JOIN permissions 
                     ON accounts.id = permissions.user_id WHERE accounts.id = ?", $id)
                     ->fetchArray();
                     ?>         
                  <div class="row">
                     <div class="col-sm-6">
                        <h4 class="py-3">
                           <i class="fas fa-edit"></i>
                           <?php echo $lang['EditDetails']; ?>
                           (<?php echo htmlspecialchars($details['username']); ?>)
                        </h4>
                        <form method="POST">
                           <?php if(isset($msgEmpty)) { ?>
                           <div class="alert alert-danger">
                              <?php echo $msgEmpty; ?>
                           </div>
                           <?php } ?>
                           <?php if(isset($updated)) { ?>
                           <div class="alert alert-success">
                              <?php echo $updated; ?>
                           </div>
                           <?php } ?>
                           <div class="md-form md-bg">
                              <input type="email" name="email" class="form-control" maxlength="50" value="<?php echo htmlspecialchars($details['email']); ?>">
                              <label for="email"><?php echo $lang['EmailAddr']; ?></label>
                           </div>
                           <label><?php echo $lang['Permission']; ?></label>
                           <select name="role" class="form-control">
                              <option value="0"><?php echo $lang['User']; ?></option>
                              <option value="1">Admin</option>
                           </select>
                           <div class="md-form md-bg">
                              <button type="submit" name="btn-update" class="btn btn-primary btn-block">
                              <?php echo $lang['EditDetails']; ?>
                              </button>
                           </div>
                        </form>
                     </div>
                     <div class="col-sm-6">
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
                                    ON accounts.id = account_logins.user_id WHERE account_logins.user_id = '$id'
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
                  <div class="alert alert-danger" style="margin-top: 25px">
                     <div class="row">
                        <div class="col-sm-8">
                           <h4><?php echo $lang['DeleteAcc']; ?></h4>
                           <?php echo $lang['DeleteAccText']; ?>
                        </div>
                        <div class="col-sm-4">
                           <form method="POST">
                              <button type="submit" name="btn-delete" class="btn btn-danger btn-block" <?php if ($current_account['id'] == $id) { echo "disabled"; } ?>>
                                 <?php echo $lang['DeleteAcc']; ?>
                              </button>
                           </form>
                        </div>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
   </body>
</html>
<?php $db->close(); ?>