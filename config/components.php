<?php
//Különösen fontos, hogy 0 értéke legyen:
//ha bármely fiók funkció crash-el, így nem írja ki a felhasználók adatait.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

//Alapvető komponensek
require '../languages/languages.php';
require '../src/realip.php';
require '../src/waf.class.php';
require '../src/sql.class.php';

//Időzóna a naplózáshoz
date_default_timezone_set('Europe/Budapest');

?>