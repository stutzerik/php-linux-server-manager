<?php

header('Cache-control: Private');

if (isset($_GET['lang']))
{
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;

    //Kiválasztott nyelv mentése cookie-ba
    setcookie('lang', $lang, time() + (3600 * 24 * 30));
}

elseif (isset($_SESSION['lang']))
{
    $lang = $_SESSION['lang'];
}

if (isset($_COOKIE['lang']))
{
    $lang = $_COOKIE['lang'];
}

//Alapértelmezett nyelv
else
{
    $lang = 'hu';
}

//Nyelv kiválasztása
switch ($lang)
{
    case 'hu':
        $lang_file = 'lang.hu.php';
        break;
    case 'en':
        $lang_file = 'lang.en.php';
        break;
    default:
        $lang_file = 'lang.hu.php';
}

//Választott nyelvfájl meghívása
include_once($lang_file);

?>