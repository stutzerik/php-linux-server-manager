<?php
   //Szerverek
      
   session_start();
   if(!isset($_SESSION['username']))
   {
      header('Location: /login');
      exit();
   }
      
   require '../config/components.php';
   require '../src/remote.class.php';
      
   $db = new DBconnect();
   
   //Tűzfal aktiváció
   require '../includes/integrated_fw.php';
   
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../includes/header.html'; ?>
      <title><?php echo $lang['Servers']; ?></title>
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
                     <i class="fas fa-server"></i> 
                     <?php echo $lang['Servers']; ?>
                  </h3>
                  <?php 
                     $servers = $db->query('SELECT * FROM remote_servers');
                     if($servers->numRows() == 0)
                     {
                  ?>
                  <div class="justify-content-center text-center" style="height: auto;">
                     <img src="/theme/img/servers.png" alt="Server" title="Server">
                     <br>
                     <p class="lead"><?php echo $lang['NoServers']; ?></p>
                     <a href="/server/connect" class="btn btn-primary shadow">
                     <?php echo $lang['Addserver']; ?>
                     </a>
                  </div>
                  <?php
                     }
                     else
                     {
                     ?>
                  <div class="table-responsive">
                     <table id="servers" name="servers" class="table table-bordered table-stripped">
                        <thead class="bg-dark text-white text-center">
                           <tr>
                              <th><?php echo $lang['Region']; ?></th>
                              <th><?php echo $lang['IPAddr']; ?></th>
                              <th>SSH Port</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php
                              $db->query('SELECT * FROM remote_servers')->fetchAll(function($server) {
                           ?>
                           <tr class="lead text-center">
                              <td>
                                 <span style="display: flex; align-items: center;"><span style="margin-right: 8px; font-size: 24px;" class="fi fi-<?php echo htmlspecialchars($server['flag']); ?>"></span>
                                 <?php echo htmlspecialchars($server['region']); ?></span>
                              </td>
                              <td>
                                 <?php echo htmlspecialchars($server['ip_address']); ?>
                              </td>
                              <td>
                                 <?php echo htmlspecialchars($server['ssh_port']); ?>
                              </td>
                              <td>
                                 <a class="btn btn-primary btn-sm btn-block" href="/server/manage/<?php echo htmlspecialchars($server['id']); ?>">
                                    <i class="fas fa-wrench"></i> <?php echo $lang['Manage']; ?>
                                 </a>
                              </td>
                           </tr>
                           <?php }); ?>
                        </tbody>
                     </table>
                  </div>
                  <?php
                     }
                  ?>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
         $(document).ready(function () 
         {
            $('#servers').DataTable();
         });
      </script>
   </body>
</html>
<?php $db->close(); ?>