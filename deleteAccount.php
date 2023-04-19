<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: home.php');
    exit;
}

// Remove the user from the database
require_once("../constants.php");
try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);

    $sql = "DELETE FROM user WHERE username = ?";
    $statement = $pdo->prepare($sql);

    $statement->execute([$_SESSION["username"]]);

    $pdo = null;

    // Log the user out
    header('Location: logout.php');
    exit;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


?>