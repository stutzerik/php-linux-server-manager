<?php
   //Naplózás
    
   session_start();
   if(!isset($_SESSION['username']))
   {
     header('Location: /login');
     exit();
   }
    
   require '../config/components.php';
   require '../src/system.class.php';
    
   $db = new DBconnect();

    
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Logs']; ?></title>
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
                     <i class="fas fa-list-ul"></i> 
                     <?php echo $lang['Logs']; ?>
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
                  <ul class="nav nav-tabs">
                     <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#local">
                        <?php echo $lang['LocalLogs']; ?>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#remote">
                        <?php echo $lang['RemoteLogs']; ?>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#accounts">
                        <?php echo $lang['AccountLogs']; ?>
                        </a>
                     </li>
                  </ul>
                  <div class="tab-content">
                     <div class="tab-pane container active" id="local">
                        <div class="table-responsive" style="margin-top: 25px;">
                           <table id="locallogs" name="locallogs" class="table table-bordered table-stripped">
                              <thead class="bg-dark text-white text-center">
                                 <tr>
                                    <th><?php echo $lang['Username']; ?></th>
                                    <th><?php echo $lang['IPAddr']; ?></th>
                                    <th><?php echo $lang['Action']; ?></th>
                                    <th><?php echo $lang['ActionDate']; ?></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                    $db->query('SELECT username, user_id, user_ip, action, action_date FROM localserver_logs LEFT JOIN accounts 
                                    ON localserver_logs.user_id = accounts.id ORDER BY action_date DESC')
                                    ->fetchAll(function($llog) {
                                    ?>
                                 <tr class="lead text-center">
                                    <td>
                                       <?php 
                                          echo htmlspecialchars($llog['username']);
                                          ?>
                                       <a style="margin-left: 10px;" class="btn btn-sm btn-purple" href="/user/<?php echo $llog['user_id']; ?>" target="_blank">
                                       <i class="fas fa-external-link-alt"></i>
                                       </a>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($llog['user_ip']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($llog['action']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($llog['action_date']); ?>
                                    </td>
                                 </tr>
                                 <?php }); ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="tab-pane container fade" id="remote">
                        <div class="table-responsive" style="margin-top: 25px;">
                           <table id="remotelogs" name="remotelogs" class="table table-bordered table-stripped">
                              <thead class="bg-dark text-white text-center">
                                 <tr>
                                    <th><?php echo $lang['Username']; ?></th>
                                    <th><?php echo $lang['UserIP']; ?></th>
                                    <th><?php echo $lang['ServerIP']; ?></th>
                                    <th><?php echo $lang['Action']; ?></th>
                                    <th><?php echo $lang['ActionDate']; ?></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                    $db->query('SELECT username, user_ip, user_id, ip_address, action, action_date
                                    FROM accounts, server_logs, remote_servers 
                                    WHERE accounts.id = server_logs.user_id 
                                    ORDER BY action_date DESC')
                                    ->fetchAll(function($remotelog) {
                                    ?>
                                 <tr class="lead text-center">
                                    <td>
                                       <?php 
                                          echo htmlspecialchars($remotelog['username']);
                                          ?>
                                       <a style="margin-left: 10px;" class="btn btn-sm btn-purple" href="/user/<?php echo $remotelog['user_id']; ?>" target="_blank">
                                       <i class="fas fa-external-link-alt"></i>
                                       </a>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($remotelog['user_ip']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($remotelog['ip_address']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($remotelog['action']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($remotelog['action_date']); ?>
                                    </td>
                                 </tr>
                                 <?php }); ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="tab-pane container fade" id="accounts">
                        <div class="table-responsive" style="margin-top: 25px;">
                           <table id="acclogs" name="acclogs" class="table table-bordered table-stripped">
                              <thead class="bg-dark text-white text-center">
                                 <tr>
                                    <th><?php echo $lang['Username']; ?></th>
                                    <th><?php echo $lang['IPAddr']; ?></th>
                                    <th><?php echo $lang['Action']; ?></th>
                                    <th><?php echo $lang['ActionDate']; ?></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                    $db->query('SELECT  * FROM account_logs RIGHT JOIN accounts
                                    ON account_logs.user_id = accounts.id WHERE account_logs.user_id = accounts.id ORDER BY action_date DESC')
                                    ->fetchAll(function($accountlog) {
                                    ?>
                                 <tr class="lead text-center">
                                    <td>
                                       <?php 
                                          echo htmlspecialchars($accountlog['username']);
                                          ?>
                                       <a style="margin-left: 10px;" class="btn btn-sm btn-purple" href="/user/<?php echo $accountlog['user_id']; ?>" target="_blank">
                                       <i class="fas fa-external-link-alt"></i>
                                       </a>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($accountlog['user_ip']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($accountlog['action']); ?>
                                    </td>
                                    <td>
                                       <?php echo htmlspecialchars($accountlog['action_date']); ?>
                                    </td>
                                 </tr>
                                 <?php }); ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
         $(document).ready(function () 
         {
            $('#locallogs').DataTable();
         });
         $(document).ready(function () 
         {
            $('#remotelogs').DataTable();
         });
         $(document).ready(function () 
         {
            $('#acclogs').DataTable();
         });
      </script>
   </body>
</html>
<?php $db->close(); ?>