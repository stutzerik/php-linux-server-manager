<?php
//Első felhasználói fiók hozzáadása
    
require '../../config/components.php'; 
$db = new DBconnect(); 

//Regisztráció
$error = false;
if(isset($_POST['btn-add']))
{
    $username = $_POST['username'];
    $username = strip_tags($username);
    $username = htmlspecialchars($username);

    $email = $_POST['email'];
    $email = strip_tags($email);
    $email = htmlspecialchars($email);

    $password = $_POST['password'];
    $password = strip_tags($password);    
    $password = htmlspecialchars($password);

    $role = $_POST['role'];
    $role = strip_tags($role);    
    $role = htmlspecialchars($role);

    if(empty($username))
    {
        $error = true;
        $errorUsername = $lang['FieldEmpty'];
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $error = true;
        $errorEmail = $lang['NotValidEmail'];
    }

    if(empty($password))
    {
        $error = true;
        $errorPassword = $lang['FieldEmpty'];
    }

    //Jelszókvóta
    $pwd_requirement = '/^(?=.*[A-Z]).{9,}$/';
    if(!preg_match($pwd_requirement, $password))
    {
        $error = true;
        $notStrenght = $lang['PwdStrength'];
    }

    //Létező felhasználó és email cím megvizsgálata
    $check = $db->query('SELECT * FROM accounts')->fetchAll();
    foreach ($check as $row) 
    {
        if(($row['username'] == $username) OR ($row['email'] == $email))
        {
            $error = true;
            $noAgain = $lang['NoAgain'];
        }
    }    

    if(!$error)
    {
        //Argon2ID titkosítás 
        $password_hash = password_hash($password, PASSWORD_ARGON2ID, 
        [
            'memory_cost' => 2048, 
            'time_cost' => 4, 
            'threads' => 3
        ]);

        //Insertálás az "accounts" táblával
        $db->query('INSERT INTO accounts (username, email, password) 
        VALUES (?,?,?)', $username, $email, $password_hash);
        $last_id = $db->lastInsertID();

        //Insertálás a "permissions" táblába
        $db->query('INSERT INTO permissions (user_id, role) 
        VALUES (?,?)', $last_id, 1);

        //Értesítés
        $register_success = $lang['UserAdded'];

        //Naplózás
        $log = $db->query('SELECT id FROM accounts WHERE username = ?', 
        array($_SESSION['username']))->fetchArray();
        $user_id = $last_id;
        $user_ip = real_ip();
        $insert = $db->query('INSERT INTO account_logs (user_id, user_ip, action) VALUES (?,?,?)', 
        $user_id, $user_ip, "Fiók létrehozva");

        header('refresh:3; url=/dashboard');

    }

}

?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include '../../includes/header.html'; ?>
      <title><?php echo $lang['AddUser']; ?></title>
   </head>
   <body class="bg-light">
      <?php include '../../includes/navbar.php'; ?>
         <div class="container py-5">
            <div class="card w-100">
               <div class="card-body py-5">
                    <h4 class="py-3">
                        <i class="fas fa-user-plus"></i>
                        <?php echo $lang['AddUser']; ?>
                    </h4>         
                <form method="POST" class="col-sm-6">
                    <?php if (isset($noAgain)) { ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $noAgain; ?>
                        </div>
                    <?php }
                    if (isset($register_success)) { ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $register_success; ?><br>
                            <i class="fa fa-spinner fa-spin"></i> <?php echo $lang['Redirecting']; ?>
                        </div>
                    <?php } ?> 
                    <div class="md-form md-bg">
                        <input type="text" name="username" class="form-control" maxlength="50">
                        <label for="username"><?php echo $lang['Username']; ?></label>
                        <?php if(isset($errorUsername)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i><?php echo $errorUsername; ?></span><?php } ?>
                    </div>
                    <div class="md-form md-bg"> 
                        <input type="email" name="email" class="form-control" maxlength="50">
                        <label for="email"><?php echo $lang['EmailAddr']; ?></label>
                        <?php if(isset($errorEmail)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo $errorEmail; ?></span><?php } ?>
                    </div>
                    <div class="md-form md-bg">
                        <input type="password" name="password" id="password" class="form-control" maxlength="150">
                        <label for="password"><?php echo $lang['Password']; ?></label>
                        <?php if(isset($errorPassword)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo $errorPassword; ?></span><?php } ?>
                        <?php if(isset($notStrenght)) { ?><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo $notStrenght; ?></span><?php } ?>
                    </div>
                    <span class="btn btn-sm btn-cyan" onclick="showpwd()">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div class="alert alert-info">
                        <?php echo $lang['PwdTips']; ?>
                    </div>
                    <div class="md-form md-bg">
                        <button type="submit" name="btn-add" class="btn btn-primary btn-block">
                            <?php echo $lang['AddUser']; ?>
                        </button>
                    </div>
                </form>
            </div>
         </div>
      </div>
      <?php include '../../includes/footer.php'; ?>
      <script>
        function showpwd() 
        {
            var x = document.getElementById("password");
            if (x.type === "password") 
            {
                x.type = "text";
            } 
            else 
            {
                x.type = "password";
            }
        } 
        //Space blokkolása  
        $("input#username").on({
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
        $("input#password").on({
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
        $("input#email").on({
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
    </script>
   </body>
</html>
<?php $db->close(); ?>