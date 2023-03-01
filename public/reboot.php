<?php
  //Újraindítás
   
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
   
  if(isset($_POST['btn-reboot']))
  {
   
    $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
    array($_SESSION['username']))->fetchArray();
    $id = $log['id'];
    $user_ip = real_ip();
    $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
    $id, $user_ip, "Lokális szerver újraindítása");
     
    $system->reboot();

  }
   
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Reboot']; ?></title>
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
                     <i class="fas fa-power-off"></i>
                     <?php echo $lang['Reboot']; ?>
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
                  <p class="lead py-3">
                     <?php echo $lang['CanReboot']; ?>
                  </p>
                  <button class="btn btn-warning" data-toggle="modal" data-target="#rebootModal">
                  <?php echo $lang['Reboot']; ?>
                  </button>
                  <div class="modal" id="rebootModal">
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
                              <?php echo $lang['SureReboot']; ?>
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
                  <?php
                     }
                  ?>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
   </body>
</html>
<?php $db->close(); ?>