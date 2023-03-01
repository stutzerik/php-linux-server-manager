<?php

//Bejelentkezés meghívása, ha a böngészőben nincs session 
if(!isset($_SESSION['username']))
{
    header('Location: /login');
    exit();
}
else 
{
    header('Location: /dashboard');
    exit();
}


?>