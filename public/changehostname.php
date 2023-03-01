<?php

//Root jelszó változtatás

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

$error = false;
if(isset($_POST['btn-changehn']))
{
    $hostname = trim($_POST['hostname']);    
    $hostname = htmlspecialchars(strip_tags($hostname));

    if(empty($hostname))
    {
        $error = true;
        $emptyHostname = $lang['FieldEmpty'];
    }

    if(!$error)
    {
        $system->change_hostname($hostname);

        $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
        array($_SESSION['username']))->fetchArray();
        $id = $log['id'];
        $user_ip = real_ip();
        $insert = $db->query('INSERT INTO localserver_logs (user_id, user_ip, action) VALUES (?,?,?)', 
        $id, $user_ip, "Hostname megváltoztatása");

        $hnchanged = $lang['HostnameChanged'];

    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include '../includes/header.html'; ?>
  <title><?php echo $lang['ChangeHostname']; ?></title>
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
                      <i class="fas fa-network-wired"></i> 
                      <?php echo $lang['ChangeHostname']; ?>
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
                        <form method="POST" class="col-sm-6">
                            <p class="lead py-3">
                                <?php echo $lang['CurrentHostname']; ?>:
                                <?php $system->current_hostname(); ?>
                            </p>
                            <?php
                               if (isset($hnchanged))
                               { ?> 
                                <div class="alert alert-success">
                                    <?php echo $hnchanged; ?>
                                </div>
                            <?php } ?>
                            <div class="md-form md-bg" style="margin-top: 10px;">
                                <input type="text" name="hostname" class="form-control" maxlength="50">
                                <label for="hostname"><?php echo $lang['NewHostname']; ?></label>
                            </div>
                            <?php if(isset($emptyHostname)) { ?> 
                                <span class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo $emptyHostname; ?>
                                </span><br>
                            <?php } ?>
                            <button type="submit" class="btn btn-primary" name="btn-changehn">
                                <?php echo $lang['ChangeHostname']; ?>
                            </button>
                        </form>
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
        $("input#hostname").on({
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

        $(document).ready(function()
        {
            $("#btn-delete").click(function()
            {
                $('.toast').toast('show');
            });
        });
    </script>
    </body>
</html>
<?php $db->close(); ?>