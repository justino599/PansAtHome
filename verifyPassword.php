<?php

require_once("../constants.php");

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo 'not logged in';
    exit;
}

// Check that a password was entered
if (!isset($_POST['password'])) {
    echo 'no password';
    exit;
}

// Get user info from database
try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);

    $sql = "SELECT password FROM user WHERE username = ?";
    $statement = $pdo->prepare($sql);

    $statement->execute([$_SESSION["username"]]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $password = $result["password"];
    } else {
        echo "Error: User not found";
        exit;
    }

    $pdo = null;

    // Check that the password is correct
    if (password_verify($_POST['password'], $password)) {
        echo 'success';
    } else {
        echo 'failure';
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>