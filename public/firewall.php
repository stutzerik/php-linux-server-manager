<?php
   //Tűzfal
    
   session_start();
   if(!isset($_SESSION['username']))
   {
     header('Location: /login');
     exit();
   }
    
   require '../config/components.php';
    
   $db = new DBconnect();
   
   if(isset($_POST['btn-update']))
   {
   
    $firewall = $_POST['firewall'];
   
    if(isset($firewall))
    {
      //SQL frissítése
      $db->query("UPDATE firewall SET status = 1 WHERE id = '1'");
    }
    else
    {
      $db->query("UPDATE firewall SET status = 0 WHERE id = '1'");
    }
   
    //Naplózás
    $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
    array($_SESSION['username']))->fetchArray();
    $id = $log['id'];
    $user_ip = real_ip();
    $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
    $id, $user_ip, "Tűzfal beállítás módosítása");
   
    //Éretesítés
    $fw_updated = $lang['FWUpdated'];
   
   }
    
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Firewall']; ?></title>
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
                     <i class="fas fa-fingerprint"></i> 
                     <?php echo $lang['Firewall']; ?>
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
                  <div class="card-body border border-warning rounded-sm py-3">
                     <form method="POST">
                        <?php if (isset($fw_updated))
                           { ?> 
                        <div class="alert alert-success">
                           <i class="fas fa-check-circle"></i>
                           <?php echo $fw_updated; ?>
                        </div>
                        <?php } ?>
                        <div class="py-3">
                           <h4><i class="fas fa-lock text-warning"></i>
                              <?php echo $lang['UnderAttack']; ?>
                           </h4>
                           <hr>
                        </div>
                        <div class="custom-control custom-switch">
                           <?php
                              $enabled = $db->query('SELECT status FROM firewall WHERE id = ?', 
                              array('1'))->fetchArray();
                              if($enabled['status'] == 1)   
                              {
                              ?>
                            <input type="checkbox" class="custom-control-input" name="firewall" id="firewall" checked>
                              <label class="custom-control-label lead" for="firewall"> 
                           <span class="text-bold text-success"><?php echo $lang['Enabled']; ?></span>
                           <?php
                              }
                              else
                              {
                              ?>     
                           <input type="checkbox" class="custom-control-input" name="firewall" id="firewall">
                           <label class="custom-control-label lead" for="firewall">
                           <span class="text-bold text-danger"><?php echo $lang['Disabled']; ?></span>
                           <?php } ?>
                           <br> <?php echo $lang['BlockText']; ?></label>
                        </div>
                        <button type="submit" name="btn-update" class="btn btn-success">
                           <?php echo $lang['Save']; ?>
                        </button>
                     </form>
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