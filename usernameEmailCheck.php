<?php

if (!isset($_POST['username']) || !isset($_POST['email'])) {
    echo "Invalid request";
    exit();
}

require_once("../constants.php");

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);

    $sql = "SELECT COUNT(*) AS userexists FROM user WHERE username = :username AND email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $_POST['username'], 'email' => $_POST['email']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['userexists'] >= 1) {
        echo "true";
    } else {
        echo "false";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>