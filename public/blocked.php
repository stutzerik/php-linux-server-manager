<?php
   //Blokkolási üzenetek
      
   session_start();
   require '../config/components.php';      
   
   $msg = $_GET['msg'];
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['AccessBlocked']; ?></title>
   </head>
   <body class="bg-light">
      <?php include '../includes/navbar.php'; ?>
      <div class="container py-5">
      <div class="py-3">
         <div class="alert alert-danger">
            <?php echo $lang['AccessBlocked']; ?>
         </div>
      </div>
        <?php 
         if($msg == "proxy")
         {
            echo $lang['ProxyBanned'];
         }
         if($msg == "tor")
         {
            echo $lang['TorBanned'];
         }
        ?>
      </div>
      <?php include '../includes/footer.php'; ?>
   </body>
</html>
