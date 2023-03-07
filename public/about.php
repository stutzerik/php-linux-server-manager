<?php
  session_start();
  require '../config/components.php';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <title><?php echo $lang['ProjectInfo']; ?></title>
    <?php include '../includes/header.html'; ?>
</head>
<body class="bg-light">
  <?php include '../includes/navbar.php'; ?>
    <div id="particles-js"></div> 
    <div class="container">
    <?php 
      if($_SESSION['lang'] == "hu") { ?>
        <div class="card shadow" style="margin-top: 50px;">
            <div class="card-header">
                A NextCaligo szoftverről
            </div>
            <div class="card-body">
                A szoftver Stütz Erik szakdolgozata és szellemi tulajdona, amely 
                2022/23-as tanév szoftverfejlesztő- és tesztelő OKJ szakmai vizsga követelményei 
                és referencia készítés miatt került kifejlesztésre.
                <br>
                A szoftver távoli Linux kiszolgálók menedzsmentjére hivatott, valamint képes a helyi (központi)
                szerver menedzsmentjére is, továbbá képes azon virtuális szervereket (VPS) létrehozni - így 
                teljeskörű adatközponti infrastruktúra menedzsmentet kínálva használóinak.
                <hr>
                <b>Verzió: BETA 1.0.0</b>
            </div>
        </div>
        <div class="card shadow" style="margin-top: 50px;">
            <div class="card-header">
                Dokumentáció és használat
            </div>
            <div class="card-body">
                A mellékelt fejlesztői és felhasználói útmutató a szoftver főkönyvtárában megtalálható.
            </div>
        </div>
        <div class="card shadow" style="margin-top: 50px; margin-bottom: 50px;">
            <div class="card-header">
                Források & felhasznált könyvtárak
            </div>
            <div class="card-body">
                Az alábbi könyvtárak kerültek felhasználásra a fejlesztés során - mind MIT licencel,
                vagy egyedi nyílt forráskodú, a terjesztést, a használatot és módosítást is engedélyező
                licencel rendelkeznek. Jelen táblázattal teszek eleget a licenszben foglaltaknak (feltüntetés).
                Minden más fájl a saját szellemi tulajdonom. <br>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light lead">
                      <tr>
                        <th>Funkció</th>
                        <th>Forrás megnevezése</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>CSS ikonok</td>
                        <td>FontAwesome 5 Free (CC BY 4.0)</td>
                      </tr>
                      <tr>
                        <td>JavaScript táblakezelés</td>
                        <td>DataTables 1 (MIT)</td>
                      </tr>
                      <tr>
                        <td>Ország zászlók</td>
                        <td>lipis/flag-icons (MIT)</td>
                      </tr>
                      <tr>
                        <td>Linux disztribució illusztrációk</td>
                        <td>lukas-w/font-logos (MIT)</td>
                      </tr>
                      <tr>
                        <td>Kimutatás-ábrák</td>
                        <td>ApexCharts (MIT)</td>
                      </tr>
                      <tr>
                        <td>Egyéb frontend</td>
                        <td>MDB 4, Bootstrap 4, JQuery 3 (MIT)</td>
                      </tr>
                      <tr>
                        <td>JavaScript háttér</td>
                        <td>ParticlesJS könyvtár (MIT)</td>
                      </tr>
                      <tr>
                        <td>Captcha kód</td>
                        <td>SecureImage (BSD)</td>
                      </tr>
                      <tr>
                        <td>TOR detektáló</td>
                        <td>dapphp/torutils (BSD)</td>
                      </tr>
                      <tr>
                        <td>Email küldő</td>
                        <td>PhPMailer (GNU Lesser General Public License v2.1)</td>
                      </tr>
                      <tr>
                        <td>SQL lekérdező</td>
                        <td>CodeShack (Saját, terjesztés engedélyezve)</td>
                      </tr>
                      <tr>
                        <td>Képek, illusztrációk</td>
                        <td>Flaticon, Storyset ("designed by Flat Icons, Smashicons, Freepik from Flaticon")</td>
                      </tr>
                      <tr>
                        <td>Térkép API</td>
                        <td>
                          This product includes GeoLite data created by MaxMind, available from
                          <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
        <p class="text-center">Köszönöm, hogy elolvasta!</p>
        <?php } else { ?>
          <div class="card shadow" style="margin-top: 50px;">
            <div class="card-header">
              About NextCaligo software
            </div>
            <div class="card-body">
                The software is the thesis and intellectual property of 
                Erik Stütz, which has been developed for the requirements of the 2022/23 school 
                year for software development and testing OKJ professional exam and reference.
                <br>
                The software is for the management of remote Linux servers, 
                and is capable of the management of the local (central) server, 
                and is capable of creating virtual servers (VPS) - thus providing full datacenter 
                infrastructure management.
                <hr>
                <b>Version: BETA 1.0.0</b>
            </div>
        </div>
        <div class="card shadow" style="margin-top: 50px;">
            <div class="card-header">
              Documentation and use
            </div>
            <div class="card-body">
              The developer and user guide can be found in the software directory.            
            </div>
        </div>
        <div class="card shadow" style="margin-top: 50px; margin-bottom: 50px;">
            <div class="card-header">
              Sources & libraries used
            </div>
            <div class="card-body">
            The following libraries have been used during the development - they all have a MIT license 
            or unique open source, allowing distribution, 
            use and modification. With this table, I comply with the license (indication).<br>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light lead">
                      <tr>
                        <th>Function</th>
                        <th>Naming source</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>CSS icons</td>
                        <td>FontAwesome 5 Free (CC BY 4.0)</td>
                      </tr>
                      <tr>
                        <td>JavaScript tables</td>
                        <td>DataTables 1 (MIT)</td>
                      </tr>
                      <tr>
                        <td>Country flags</td>
                        <td>lipis/flag-icons (MIT)</td>
                      </tr>
                      <tr>
                        <td>Linux distribution illustrations</td>
                        <td>lukas-w/font-logos (MIT)</td>
                      </tr>
                      <tr>
                        <td>Resource graph</td>
                        <td>ApexCharts (MIT)</td>
                      </tr>
                      <tr>
                        <td>Other frontend</td>
                        <td>MDB 4, Bootstrap 4, JQuery 3 (MIT)</td>
                      </tr>
                      <tr>
                        <td>JavaScript background</td>
                        <td>ParticlesJS directory (MIT)</td>
                      </tr>
                      <tr>
                        <td>Captcha code</td>
                        <td>SecureImage (BSD)</td>
                      </tr>
                      <tr>
                        <td>TOR detection</td>
                        <td>dapphp/torutils (BSD)</td>
                      </tr>
                      <tr>
                        <td>Email sending</td>
                        <td>PhPMailer (GNU Lesser General Public License v2.1)</td>
                      </tr>
                      <tr>
                        <td>SQL query</td>
                        <td>CodeShack (Custom, can be distributed with a designation)</td>
                      </tr>
                      <tr>
                        <td>Pictures, illustrations</td>
                        <td>Flaticon, Storyset ("designed by Flat Icons, Smashicons, Freepik from Flaticon")</td>
                      </tr>
                      <tr>
                        <td>Map API</td>
                        <td>
                          This product includes GeoLite data created by MaxMind, available from
                          <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
        <p class="text-center">Thank you for reading it!</p>
        <?php } ?>
    </div>
    <script src="/theme/particlesjs/particles.min.js"></script>
    <script src="/theme/particlesjs/config.min.js"></script>
</body>
</html>