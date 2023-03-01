<?php
  //KVM Virtuális gép létrehozása
   
  session_start();
  if(!isset($_SESSION['username']))
  {
    header('Location: /login');
    exit();
  }
   
  require '../config/components.php';
  require '../src/virtualization.class.php';
  $db = new DBconnect();
  $kvm = new KVM();
    
  //Tűzfal aktiváció
  require '../includes/integrated_fw.php';
  if(isset($_POST['btn-deploy']))
  {
        $vm_name = htmlspecialchars($_POST['vm_name']);
        $vcpu = htmlspecialchars($_POST['vcpu']);
        $memory = htmlspecialchars($_POST['memory']);
        $disk_size = htmlspecialchars($_POST['disk_size']);
        $iso = htmlspecialchars($_POST['iso']);

        if(empty($vm_name))
        {
            $error = true;
            $emptyName = $lang['FieldEmpty'];
        }

        if(empty($vcpu))
        {
            $error = true;
            $emptyCPU = $lang['FieldEmpty']; 
        }

        if(empty($memory))
        {
            $error = true;
            $emptyMem = $lang['FieldEmpty'];
        }

        if(empty($disk_size))
        {
            $error = true;
            $emptyDisk = $lang['FieldEmpty'];
        }

        $check = $db->query('SELECT * FROM machines')->fetchAll();
        foreach ($check as $row) 
        {
            if($row['vm_name'] == $vm_name)
            {
                $error = true;
           }
        }  

        if(!$error)
        {
            $name = $vm_name;
            $kvm->createVM($name, $vcpu, $memory, $disk_size, $iso);
            $insert = $db->query('INSERT INTO machines (vm_name, vcpu, memory, disk_size, operating_system) 
            VALUES (?,?,?,?,?)', $vm_name, $vcpu, $memory, $disk_size, $iso);
            $vm_id = $db->lastInsertID();

            header("refresh:3; url=/machine/{$vm_id}");
            $deploy_success = $lang['DeploySuccess'];
        }
  }
   
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
                     <?php echo $lang['DeployVM']; ?>
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
                    <div class="py-3">
                        <form method="POST" class="col-sm-6">
                        <?php  
                            if (isset($deploy_success)) { ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo $deploy_success; ?><br>
                                    <i class="fa fa-spinner fa-spin"></i> <?php echo $lang['Redirecting']; ?>
                                </div>
                           <?php } ?>
                        <div class="md-form md-bg">
                            <input type="text" name="vm_name" class="form-control" maxlength="50">
                            <label for="vm_name"><?php echo $lang['VMName']; ?></label>
                            <?php if(isset($emptyName)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $emptyName; ?></span><?php } ?>
                        </div>
                       <div class="md-form md-bg">
                            <input type="number" name="vcpu" class="form-control" maxlength="2" min="1" max="8">
                            <label for="vcpu"><?php echo $lang['vCPUCores']; ?></label>
                            <?php if(isset($emptyCPU)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $emptyCPU; ?></span><?php } ?>
                        </div>
                        <div class="md-form md-bg">
                              <input type="number" name="memory" class="form-control" maxlength="6" min="128" step="128" minlegth="3"> 
                              <label for="memory"><?php echo $lang['Memory']; ?> (MB)</label>
                              <?php if(isset($emptyMem)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $emptyMem; ?></span><?php } ?>
                           </div>
                        <div class="md-form md-bg">
                            <input type="number" name="disk_size" class="form-control" maxlength="24" step="5" min="5" max="1000">
                            <label for="disk_size"><?php echo $lang['DiskSize']; ?> (GB)</label>
                            <?php if(isset($emptyDisk)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $emptyDisk; ?></span><?php } ?>
                        </div>
                        <label><?php echo $lang['OperatingSys']; ?></label>
                        <select name="iso" class="form-control">
                            <option value="debian-11.6.0-amd64-netinst.iso">debian-11.6.0-amd64-netinst</option>
                            <option value="ubuntu-20.04.5-live-server-amd64.iso">ubuntu-20.04.5-live-server-amd64</option>
                        </select>
                        <div class="md-form">
                        <button type="submit" name="btn-deploy" class="btn btn-block btn-success">
                            <?php echo $lang['DeployVM']; ?>
                        </button>
                    </div>
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
      <script>
        //Space blokkolása  
        $("input#vm_name").on({
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
        
        //Különleges karakterek blokkolása
        $('input').on('keypress', function (event) 
        {
            var regex = new RegExp("^[a-zA-Z0-9,.]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) 
            {
                event.preventDefault();
                return false;
            }
        });
    </script>
   </body>
</html>
<?php $db->close(); ?>