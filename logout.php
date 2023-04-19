<?php
session_start();
$_SESSION["loggedin"] = false;
$_SESSION["username"] = null;
$_SESSION["admin"] = false;

header('Location: home.php');
?>