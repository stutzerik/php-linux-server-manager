<div class="sidenav">
    <?php 
        $account = $db->query('SELECT * FROM accounts LEFT JOIN permissions 
        ON accounts.id = permissions.user_id WHERE accounts.username = ?', 
        array($_SESSION['username']))->fetchArray();
        $permission = $account['role'];
    ?>
    <a href="/dashboard"><i class="fas fa-tachometer-alt"></i> <?php echo $lang['Dashboard']; ?></a>
    <a href="/servers"><i class="fas fa-server"></i> <?php echo $lang['Servers']; ?></a>
    <?php if($permission == 1) { ?>
        <a href="/server/connect"><i class="fas fa-plug"></i> <?php echo $lang['Addserver']; ?></a>
        <a href="/rootpasswd"><i class="fas fa-key"></i> <?php echo $lang['RootPwd']; ?></a>
        <a href="/changehostname"><i class="fas fa-network-wired"></i> <?php echo $lang['ChangeHostname']; ?></a>
    <?php } ?>
    <a href="/stats"><i class="fas fa-signal"></i> <?php echo $lang['Stats']; ?></a>
    <?php if($permission == 1) { ?>
        <a href="/reboot"><i class="fas fa-power-off"></i> <?php echo $lang['Reboot']; ?></a>
    <?php } ?>
        <a href="/machines"><i class="fas fa-cloud"></i> <?php echo $lang['VMs']; ?></a>
    <?php if($permission == 1) { ?>
        <a href="/firewall"><i class="fas fa-fingerprint"></i> <?php echo $lang['Firewall']; ?></a>
        <a href="/logs"><i class="fas fa-list-ul"></i> <?php echo $lang['Logs']; ?></a>
        <a href="/accounts"><i class="fas fa-users"></i> <?php echo $lang['Users']; ?></a>
    <?php } ?>
    <a href="/account/my"><i class="fas fa-user"></i> <?php echo $lang['MyAccount']; ?></a>
</div>