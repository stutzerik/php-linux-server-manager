<?php
  //KVM Virtuális gépek 
   
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
      <title><?php echo $lang['VMs']; ?></title>
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
                     <i class="fas fa-cloud"></i>
                     <?php echo $lang['VMs']; ?>
                  </h3>
                    <div class="table-responsive" style="margin-top: 25px;">
                           <table id="machines" name="machines" class="table table-bordered table-stripped">
                              <thead class="bg-dark text-white text-center">
                                 <tr>
                                    <th><?php echo $lang['VMName']; ?></th>
                                    <th>vCPU</th>
                                    <th><?php echo $lang['Memory']; ?></th>
                                    <th><?php echo $lang['Disk']; ?></th>
                                    <th><?php echo $lang['OperatingSys']; ?></th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                    $db->query('SELECT * FROM machines')
                                    ->fetchAll(function($vm) {
                                    ?>
                                 <tr class="lead text-center">
                                    <td>
                                       <?php echo htmlspecialchars($vm['vm_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($vm['vcpu']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($vm['memory']); ?> MB
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($vm['disk_size']); ?> GB
                                    </td>
                                    <td>
                                    <?php 
                                            $os = htmlspecialchars($vm['operating_system']); 
                                            if(str_contains($os, "debian"))
                                            {
                                                echo '<i class="fl-debian fl-36 text-primary"></i>';
                                            }
                                            if(str_contains($os, "ubuntu"))
                                            {
                                                echo '<i class="fl-ubuntu fl-36 text-primary"></i>';
                                            }
                                            if(str_contains($os, "centos"))
                                            {
                                                echo '<i class="fl-centos fl-36 text-primary"></i>';
                                            }
                                            if(str_contains($os, "arch"))
                                            {
                                                echo '<i class="fl-archlinux fl-36 text-primary"></i>';
                                            }
                                            if(str_contains($os, "windows"))
                                            {
                                                echo '<i class="fab fa-windows fl-36 text-primary"></i>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                       <a class="btn btn-primary btn-sm btn-block" href="/machine/<?php echo htmlspecialchars($vm['id']); ?>">
                                          <i class="fas fa-wrench"></i>
                                       </a>
                                    </td>
                                 </tr>
                                 <?php }); ?>
                              </tbody>
                           </table>
                        </div>
                        <a class="btn btn-primary" href="/deploy">
                           <?php echo $lang['DeployVM']; ?>
                        </a>
               </div>
            </div>
         </div>
      </div>
      <?php include '../includes/footer.php'; ?>
      <script>
        $(document).ready(function () 
        {
            $('#machines').DataTable();
        });
      </script>
   </body>
</html>
<?php $db->close(); ?>