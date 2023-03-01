<?php

//Angol nyelv
//English 

$lang = array();

//Sidebar
$lang['Dashboard'] = 'Dashboard';
$lang['Servers'] = 'Remote servers';
$lang['Addserver'] = 'Connect server';
$lang['RootPwd'] = 'Root password';
$lang['ChangeHostname'] = 'Change hostname';
$lang['Stats'] = 'Resource & statistics';
$lang['Reboot'] = 'Reboot';
$lang['VMs'] = 'Virtual servers';
$lang['Firewall'] = 'Firewall & security';
$lang['Logs'] = 'Logs';
$lang['Users'] = 'Accounts';
$lang['MyAccount'] = 'My accounts';

//Account login
$lang['Login'] = 'Sign in';
$lang['EmailAddr'] = 'Email address';
$lang['Password'] = 'Password';
$lang['CaptchaCode'] = 'Captcha code';
$lang['NewCode'] = 'New code';
$lang['ForgotPwd'] = 'Forgot your password?';
$lang['IPLogging'] = 'Your device data and IP address will be logged when you sign in.';
$lang['FieldEmpty'] = 'This field cannot be left blank.';
$lang['NotValidEmail'] = 'Please enter a valid email address!';
$lang['ErrorCaptcha'] = 'Incorrect Captcha code. Please enter the characters shown in the picture!';
$lang['ErrorLogin'] = 'Wrong e-mail address or password!';
$lang['LcRemaining'] = 'Number of attempts left: ';
$lang['LoginBlocked'] = 'You have been banned for 10 minutes due to botched login attempts.';
$lang['Welcome'] = 'Welcome, ';
$lang['Logout'] = 'Logout';
$lang['LoginSuccess'] = 'Successful login for your account.';
$lang['Redirecting'] = 'Redirecting...';
$lang['NoPermission']  = 'You do not have the appropriate authority to perform this operation.';
$lang['Back'] = 'Back';

//Forgot password
$lang['SendMail'] = 'Send a reminder';
$lang['MailSent'] = 'Reminder sent successfully. Check your email account!';
$lang['InvalidCode'] = 'Invalid code. You have already changed your password or clicked on an invalid link.';

//Dashboard
$lang['NoServersFound'] = 'No server(s) added.';
$lang['ManageServers'] = 'Manage servers';
$lang['ManageUsers'] = 'Manage user accounts';

//Root password
$lang['NewRootPwd'] = 'New root password';
$lang['RootPwdAgain'] = 'Root password again';
$lang['PwdNotMatch'] = 'The entered passwords do not match.';
$lang['PwdStrength'] = 'The password must be at least 9 characters long and contain at least one capital letter and number!';
$lang['RootPwdChanged'] = 'The root password has been successfully changed!';
$lang['ChangePwd'] = 'Change password';
$lang['PwdTips'] = 'For a strong password, it is recommended to use letters, numbers and special characters (?!#).';

//Hostname change
$lang['NewHostname'] = 'New hostname';
$lang['CurrentHostname'] = 'Current hostname';
$lang['HostnameChanged'] = 'Hostname successfully changed!';

//Stats
$lang['CPUUsage'] = 'CPU usage';
$lang['MemUsage'] = 'Memory usage';
$lang['DiskUsage'] = 'Disk usage';
$lang['CPUModel'] = 'CPU type';
$lang['Cores'] = ' CPU core(s)';
$lang['Uptime'] = 'Uptime';
$lang['Days'] = ' day';

//Reboot
$lang['CanReboot'] = 'You can restart this Linux server if necessary.';
$lang['SureReboot'] = 'Are you sure you want to restart this server? The web server will be unavailable during the restart and unsaved files may be lost.';
$lang['RebootCMDSent'] = 'The restart command has been sent to the server!';

//Connect server
$lang['IPAddr'] = 'IPv4 address';
$lang['Region'] = 'City or region';
$lang['Country'] = 'Country';
$lang['EmptyFields'] = 'No blank fields can be left.';
$lang['NoLocalServer'] = 'Unable to add local server. You can manage it from the side menu.';
$lang['HostAdded'] = 'Remote server successfully added to the system!';
$lang['SystemError'] = 'A system error has occurred. Please try again!';
$lang['NoIPRepeat'] = 'This IP address has already been added to the system.';
$lang['OnlyLinux'] = 'Only Linux servers with SSH access can be added to the system.';
$lang['PwdChars'] = 'The use of the & character is not allowed in the password.';

//Servers
$lang['NoServers'] = 'No servers added yet.';
$lang['Manage'] = 'Manage';
$lang['Delete'] = 'Delete';

//Manage servers
$lang['ManageServer'] = 'Manage server';
$lang['ServerStatus'] = 'Server status';
$lang['Details'] = 'Details';
$lang['DeleteServer'] = 'Delete server';
$lang['PowerOff'] = 'Power off';
$lang['SurePowerOff'] = 'Are you sure you want to stop this server? Unsaved data can be lost, and since this is a physical server, it can no longer be started from this interface.';
$lang['SureRebootRemote'] = 'Are you sure you want to restart this server? Unsaved data may be lost.';
$lang['CommandSent'] = 'Command successfully sent, operation executed!';
$lang['DeleteServerText'] = 'I do not need this server: the server and its login data will be deleted, after which you will not be able to manage the server.';

//Firewall
$lang['FWUpdated'] = 'Firewall settings successfully updated!';
$lang['Enabled'] = 'Enabled';
$lang['Disabled'] = 'Disabled';
$lang['UnderAttack'] = '"Under attack" mode';
$lang['BlockText'] = 'Block some VPN and Proxy connections on the website.';
$lang['Save'] = 'Save';

//Virtual servers
$lang['VMName'] = 'VM name';
$lang['Memory'] = 'Memory';
$lang['Disk'] = 'Disk';
$lang['OperatingSys'] = 'Operating system';
$lang['ManageVM'] = 'Manage virtual server';
$lang['Backup'] = 'Backup';
$lang['Started'] = 'Started';
$lang['Stopped'] = 'Stopped';
$lang['Created'] = 'Created';
$lang['StartVM'] = 'Start';
$lang['StopVM'] = 'Stop';
$lang['RestartVM'] = 'Reboot';
$lang['VMStarted'] = 'Virtual server successfully started!';
$lang['VMStopped'] = 'Virtual server stopped successfully!';
$lang['VMRestarted'] = 'Virtual server restarted successfully!';
$lang['CreateBackup'] = 'Create backup';
$lang['BackupSuccess'] = 'Backup successfully created with the name: ';
$lang['BackupName'] = 'Backup name';
$lang['VMRestored'] = 'Virtual server restore was successful for this time: ';
$lang['MemoryExp'] = 'Memory expansion';
$lang['CurrentMemory'] = 'Current memory: ';
$lang['ScaleSuccess'] = 'Successful memory expansion!';
$lang['DeleteVM'] = 'If you delete the server, all data will be irretrievably lost.';

//VM creation
$lang['DeployVM'] = 'Create a virtual server';
$lang['DiskSize'] = 'Hard drive size';
$lang['vCPUCores'] = 'vCPU core(s)';
$lang['DeploySuccess'] = 'Virtual server successfully created!';

//Logs
$lang['LocalLogs'] = 'Local server';
$lang['RemoteLogs'] = 'Remote servers';
$lang['AccountLogs'] = 'Account logs';
$lang['Username'] = 'Username';
$lang['Action'] = 'Activity';
$lang['ActionDate'] = 'Activity date';
$lang['UserIP'] = 'User IP address';
$lang['ServerIP'] = 'Server IP address';

//Accounts
$lang['AccCreated'] = 'Account created';
$lang['Permission'] = 'Permission';
$lang['User'] = 'User';
$lang['EditDetails'] = 'Edit account details';
$lang['ProfileUpdated'] = 'Account successfully modified!';
$lang['Date'] = 'Date';
$lang['Device'] = 'Device';
$lang['LastLogins'] = 'Recent logins';
$lang['DeleteAcc'] = 'Delete account';
$lang['DeleteAccText'] = 'Are you sure to delete this account? Everything except log entries will be irretrievably deleted.';

//Register 
$lang['AddUser'] = 'Add account';
$lang['UserAdded'] = 'Account successfully added!';
$lang['NoAgain'] = 'It is not possible to create an account with duplicate data.';

//My account
$lang['NewPwd'] = 'Enter a new password';
$lang['PwdError'] = 'Incorrect account password.';
$lang['ChangeSuccess'] = 'Your account password updated successfully!';

//Blocking
$lang['AccessBlocked'] = 'Access denied';
$lang['TorBanned'] = 'The use of the TOR browser is prohibited.';
$lang['ProxyBanned'] = 'The use of Proxies or VPNs is prohibited.';

//Footer
$lang['Slogen'] = 'Linux server & VPS management interface';
$lang['ProjectInfo'] = 'More information about the project'; 

?>