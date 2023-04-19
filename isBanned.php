<?php

session_start();

if (!isset($_GET['username'])) {
    echo 'Invalid request';
    exit;
}

// Cannot run if you are not the user or an admin
if (!isset($_SESSION['username']) || !($_SESSION['username'] == $_GET['username'] || $_SESSION['admin'] ?? false)) {
    echo 'You do not have permission to perform this action';
    exit;
}

// This function checks if the user is banned or not
require_once("../constants.php");

try {
    $pdo = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);

    $sql = "select banned from user where username = :username";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':username', $_GET['username']);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result['banned'] == 1) {
        echo 'true';
    } else {
        echo 'false';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}


?>