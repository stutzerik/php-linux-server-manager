<?php

//Magyar nyelv

//Nyelv mentése egy tömbbe
$lang = array();

//Oldalmenü
$lang['Dashboard'] = 'Vezérlőpult';
$lang['Servers'] = 'Szerverek';
$lang['Addserver'] = 'Szerver hozzáadása';
$lang['RootPwd'] = 'Root jelszó';
$lang['ChangeHostname'] = 'Hostname váltás';
$lang['Stats'] = 'Erőforrás & statisztika';
$lang['Reboot'] = 'Újraindítás';
$lang['VMs'] = 'Virtuális szerverek';
$lang['Firewall'] = 'Tűzfal & biztonság';
$lang['Logs'] = 'Tevékenységek';
$lang['Users'] = 'Felhasználók';
$lang['MyAccount'] = 'Saját fiókom';

//Bejelentkezés
$lang['Login'] = 'Bejelentkezés';
$lang['EmailAddr'] = 'Email cím';
$lang['Password'] = 'Jelszó';
$lang['CaptchaCode'] = 'Captcha kód';
$lang['NewCode'] = 'Új kód';
$lang['ForgotPwd'] = 'Elfelejtette a jelszavát?';
$lang['IPLogging'] = 'Az Ön eszközének adatai és IP címe naplózva lesz a bejelentkezéskor.';
$lang['FieldEmpty'] = 'Ez a mező nem maradhat üres.';
$lang['NotValidEmail'] = 'Kérem, működő email címet adjon meg!';
$lang['ErrorCaptcha'] = 'Hibás Captcha kód. Kérem, írja be a képen látható karaktereket!';
$lang['ErrorLogin'] = 'Hibás email cím vagy jelszó!';
$lang['LcRemaining'] = 'Hátralévő próbálkozások száma: ';
$lang['LoginBlocked'] = 'Ön ki lett tiltva 10 percre az elrontott belépési kísérletek miatt.';
$lang['Welcome'] = 'Üdvözöljük, ';
$lang['Logout'] = 'Kijelentkezés';
$lang['LoginSuccess'] = 'Sikeres bejelentkezés!';
$lang['Redirecting'] = 'Átirányítás...';
$lang['NoPermission']  = 'Ehhez a művelethez nem rendelkezik a megfelelő jogkörrel.';
$lang['Back'] = 'Vissza';

//Elfelejtett jelszó
$lang['SendMail'] = 'Emlékeztető küldése';
$lang['MailSent'] = 'Emlékeztető sikeresen elküldve. Nézze meg email fiókját!';
$lang['InvalidCode'] = 'Érvénytelen kód. Már megváltoztatta a jelszavát, vagy érvénytelen linkre kattintott.';

//Vezérlőpult 
$lang['NoServersFound'] = 'Nincsen hozzáadott szerver.';
$lang['ManageServers'] = 'Szerverek menedzselése';
$lang['ManageUsers'] = 'Felhasználói fiókok kezelése';

//Root jelszó
$lang['NewRootPwd'] = 'Új root jelszó';
$lang['RootPwdAgain'] = 'Root jelszó ismét';
$lang['PwdNotMatch'] = 'A beírt jelszavak nem egyeznek.';
$lang['PwdStrength'] = 'A jelszónak legalább 9 karakteresnek kell lennie és tartalmazni kell legalább egy nagybetűt és számot!';
$lang['RootPwdChanged'] = 'A root jelszó sikeresen megváltozott!';
$lang['ChangePwd'] = 'Jelszó megváltoztatása';
$lang['PwdTips'] = 'Az erős jelszóhoz ajánlott betűket, számokat és speciális karaktereket (?!#) használnia.';

//Hostname váltás
$lang['NewHostname'] = 'Új hostname';
$lang['CurrentHostname'] = 'Jelenlegi hostname';
$lang['HostnameChanged'] = 'Hostname sikeresen megváltoztatva!';

//Statisztika
$lang['CPUUsage'] = 'CPU használat';
$lang['MemUsage'] = 'Memória használat';
$lang['DiskUsage'] = 'Merevlemez használat';
$lang['CPUModel'] = 'CPU típusa';
$lang['Cores'] = ' CPU mag';
$lang['Uptime'] = 'Üzemidő';
$lang['Days'] = ' nap';

//Újraindítás
$lang['CanReboot'] = 'Szükség esetén újraindíthatja ezt a Linux kiszolgálót.';
$lang['SureReboot'] = 'Biztosan újraindítja ezt a szervert? A webszerver az újraindítás ideje alatt nem lesz elérhető, és a nem mentett fájlok elveszhetnek.';
$lang['RebootCMDSent'] = 'Az újraindítási parancs elküldve a szervernek!';

//Szerver hozzáadása
$lang['IPAddr'] = 'IPv4 cím';
$lang['Region'] = 'Város vagy régió';
$lang['Country'] = 'Ország';
$lang['EmptyFields'] = 'Nem maradhatnak kitöltetlen mezők.';
$lang['NoLocalServer'] = 'Nem lehet hozzáadni a helyi kiszolgálót. Annak kezelését az oldalmenüből végezheti.';
$lang['HostAdded'] = 'Távoli szerver sikeresen hozzáadva a rendszerhez!';
$lang['SystemError'] = 'Rendszerhiba történt. Kérjük, próbálja újra!';
$lang['NoIPRepeat'] = 'Ez az IP cím már hozzá lett adva a rendszerhez.';
$lang['OnlyLinux'] = 'Csak SSH eléréssel rendelkező Linux kiszolgálók adhatók hozzá a rendszerhez.';
$lang['PwdChars'] = '& karakter használata nem engedélyezett a jelszóban.';

//Szerverek
$lang['NoServers'] = 'Még nincsenek szerverek hozzáadva.';
$lang['Manage'] = 'Kezelés';
$lang['Delete'] = 'Törlés';

//Szerver kezelése
$lang['ManageServer'] = 'Szerver kezelése';
$lang['ServerStatus'] = 'Szerver státusza';
$lang['Details'] = 'Információk';
$lang['DeleteServer'] = 'Szerver törlése';
$lang['PowerOff'] = 'Leállítás';
$lang['SurePowerOff'] = 'Biztosan leállítja ezt a szervert? A nem mentett adatok elveszhetnek, és mivel ez egy fizikai szerver, így elindítani erről a felületről már nem lehet.';
$lang['SureRebootRemote'] = 'Biztosan újraindítja ezt a szervert? A nem mentett adatok elveszhetnek.';
$lang['CommandSent'] = 'Parancs sikeresen elküldve, művelet végrehajtva!';
$lang['DeleteServerText'] = 'Nincs szükségem erre a szerverre: a szerver és a bejelentkezési adatai törlődnek, utána nincs lehetősége a szerver menedzsmentjére.';

//Tűzfal
$lang['FWUpdated'] = 'Tűzfal beállítások sikeresen frissítve!';
$lang['Enabled'] = 'Bekapcsolva';
$lang['Disabled'] = 'Kikapcsolva';
$lang['UnderAttack'] = '"Támadás alatt" mód';
$lang['BlockText'] = 'Blokkolja egyes VPN és Proxy kapcsolatokat a weboldalon.';
$lang['Save'] = 'Mentés';

//Virtuális szerverek
$lang['VMName'] = 'VM neve';
$lang['Memory'] = 'Memória';
$lang['Disk'] = 'Merevlemez';
$lang['OperatingSys'] = 'Operációs rendszer';
$lang['ManageVM'] = 'Virtuális szerver kezelése';
$lang['Backup'] = 'Biztonsági mentés';
$lang['Started'] = 'Elindítva';
$lang['Stopped'] = 'Leállítva';
$lang['Created'] = 'Létrehozva';
$lang['StartVM'] = 'Indítás';
$lang['StopVM'] = 'Leállítás';
$lang['RestartVM'] = 'Újraindítás';
$lang['VMStarted'] = 'Virtuális szerver sikeresen elindítva!';
$lang['VMStopped'] = 'Virtuális szerver sikeresen leállítva!';
$lang['VMRestarted'] = 'Virtuális szerver sikeresen újraindítva!';
$lang['CreateBackup'] = 'Biztonsági mentés készítése';
$lang['BackupSuccess'] = 'Biztonsági mentés sikeresen létrehozva ezen a néven: ';
$lang['BackupName'] = 'Biztonsági mentés neve';
$lang['VMRestored'] = 'Virtuális szerver visszaállítása sikeres volt erre az időpontra: ';
$lang['MemoryExp'] = 'Memória bővítés';
$lang['CurrentMemory'] = 'Jelenlegi memória: ';
$lang['ScaleSuccess'] = 'Sikeres memória bővítés!';
$lang['DeleteVM'] = 'Ha törli a szervert, minden adat visszaállíthatatlanul elvész.';

//VM létrehozása
$lang['DeployVM'] = 'Virtuális szerver létrehozása';
$lang['DiskSize'] = 'Merevlemez mérete';
$lang['vCPUCores'] = 'vCPU magok';
$lang['DeploySuccess'] = 'Virtuális szerver sikeresen létrehozva!';

//Naplózás
$lang['LocalLogs'] = 'Helyi szerver';
$lang['RemoteLogs'] = 'Távoli szerver';
$lang['AccountLogs'] = 'Felhasználói fiókok';
$lang['Username'] = 'Felhasználónév';
$lang['Action'] = 'Tevékenység';
$lang['ActionDate'] = 'Tevékenység dátuma';
$lang['UserIP'] = 'Felhasználó IP címe';
$lang['ServerIP'] = 'Szerver IP címe';

//Felhasználó
$lang['AccCreated'] = 'Fiók létrehozva';
$lang['Permission'] = 'Jogosultság';
$lang['User'] = 'Felhasználó';
$lang['EditDetails'] = 'Adatlap módosítása';
$lang['ProfileUpdated'] = 'Fiók sikeresen módosítva!';
$lang['Date'] = 'Dátum';
$lang['Device'] = 'Készülék';
$lang['LastLogins'] = 'Legutóbbi bejelentkezések';
$lang['DeleteAcc'] = 'Fiók törlése';
$lang['DeleteAccText'] = 'Törli a fiókot? A naplóbejegyzéseket kivéve minden visszaállíthatatlanul törlődni fog.';

//Regisztráció
$lang['AddUser'] = 'Felhasználó hozzáadása';
$lang['UserAdded'] = 'Felhasználó sikeresen hozzáadva!';
$lang['NoAgain'] = 'Nem lehet ismétlődő adatokkal fiókot létrehozni.';

//Saját fiók
$lang['NewPwd'] = 'Új jelszó megadása';
$lang['PwdError'] = 'Hibás fiók jelszó.';
$lang['ChangeSuccess'] = 'Jelszó sikeresen frissítve!';

//Blokkolás
$lang['AccessBlocked'] = 'Hozzáférés megtagadva';
$lang['TorBanned'] = 'A TOR böngésző használata tilos.';
$lang['ProxyBanned'] = 'Proxy vagy VPN használata tilos.';

//Footer
$lang['Slogen'] = 'Linux szerver & VPS kezelő';
$lang['ProjectInfo'] = 'További információ a projektről'; 

?>