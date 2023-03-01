<?php

//Kijelentkezés

session_start();

unset($_SESSION['username']);
session_unset();
header('Location: /login');

?>