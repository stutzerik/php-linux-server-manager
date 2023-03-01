<?php
   //Felhasználók
    
   session_start();
   if(!isset($_SESSION['username']))
   {
     header('Location: /login');
     exit();
   }
    
   require '../config/components.php';
    
   $db = new DBconnect();   

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Users']; ?></title>
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
                     <i class="fas fa-users"></i> 
                     <?php echo $lang['Users']; ?>
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
                        <div class="table-responsive" style="margin-top: 25px;">
                           <table id="accounts" name="accounts" class="table table-bordered table-stripped">
                              <thead class="bg-dark text-white text-center">
                                 <tr>
                                    <th><?php echo $lang['Username']; ?></th>
                                    <th><?php echo $lang['EmailAddr']; ?></th>
                                    <th><?php echo $lang['AccCreated']; ?></th>
                                    <th><?php echo $lang['Permission']; ?></th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                    $db->query('SELECT * FROM accounts LEFT JOIN permissions 
                                    ON accounts.id = permissions.user_id')
                                    ->fetchAll(function($account) {
                                    ?>
                                 <tr class="lead text-center">
                                    <td>
                                       <?php echo htmlspecialchars($account['username']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($account['email']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($account['account_created']); ?>
                                    </td>
                                    <td>
                                       <?php 
                                            if($account['role'] == 1) 
                                            {
                                                echo "Admin";
                                            }
                                       ?>
                                    </td>
                                    <td>
                                       <a class="btn btn-primary btn-sm btn-block" href="/user/<?php echo htmlspecialchars($account['id']); ?>">
                                          <i class="fas fa-wrench"></i>
                                       </a>
                                    </td>
                                 </tr>
                                 <?php }); ?>
                              </tbody>
                           </table>
                        </div>
                        <a class="btn btn-primary" href="/account/add">
                           <?php echo $lang['AddUser']; ?>
                        </a>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
         $(document).ready(function () 
         {
            $('#accounts').DataTable();
         });
      </script>
   </body>
</html>
<?php $db->close(); ?>