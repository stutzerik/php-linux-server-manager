<?php
   session_start();
   if(!isset($_SESSION['username']))
   {
       header('Location: /login');
       exit();
   }
   
   require '../config/components.php';
   
   $db = new DBconnect();
   
   //Tűzfal aktiváció
   require '../includes/integrated_fw.php';
   
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Dashboard']; ?></title>
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
                     <i class="fas fa-tachometer-alt"></i> 
                     <?php echo $lang['Dashboard']; ?>
                  </h3>
                  <div class="container-fluid">
                     <div class="row">
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/servers">
                          <img src="theme/img/icons/servers.png"> <?php echo $lang['Servers']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/server/connect">
                          <img src="theme/img/icons/addserver.png"> <?php echo $lang['Addserver']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/rootpasswd">
                          <img src="theme/img/icons/password.png"> <?php echo $lang['RootPwd']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/changehostname">
                          <img src="theme/img/icons/hostname.png"> <?php echo $lang['ChangeHostname']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/stats">
                          <img src="theme/img/icons/stats.png"> <?php echo $lang['Stats']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/reboot">
                          <img src="theme/img/icons/reboot.png"> <?php echo $lang['Reboot']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/firewall">
                          <img src="theme/img/icons/security.png"> <?php echo $lang['Firewall']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/machines">
                          <img src="theme/img/icons/vps.png"> <?php echo $lang['VMs']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/logs">
                          <img src="theme/img/icons/logs.png"> <?php echo $lang['Logs']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/accounts">
                          <img src="theme/img/icons/users.png"> <?php echo $lang['Users']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/account/add">
                          <img src="theme/img/icons/adduser.png"> <?php echo $lang['AddUser']; ?>
                        </a>
                        <a class="col-sm-3 bg-light py-3 border rounded-sm w-100 app-feature" href="/account/my">
                          <img src="theme/img/icons/account.png"> <?php echo $lang['MyAccount']; ?>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      </div>
      <?php include '../includes/footer.php'; ?> 
</html>
<?php $db->close(); ?>