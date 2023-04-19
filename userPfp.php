<?php
require_once("../constants.php");

try {
    // Connect to the database
    $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    $pdo = new PDO($conString, DBUSER, DBPASS);

    // Check if the username is already taken or the email is already in use
    $sql = 'select pfp from user where username = :username';
    $statement = $pdo->prepare($sql);

    $statement->bindValue(':username', $_GET['user']);

    $result = $statement->execute();

    if ($result) {
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            header('Content-Type: image/png');
            echo $row['pfp'];
        } else {
            header('Content-Type: image/png');
            echo file_get_contents('resources/default_user.png');
        }
    } else {
        header('Content-Type: image/png');
        echo file_get_contents('resources/default_user.png');
    }
} catch (PDOException $e) {
    header('Content-Type: image/png');
    echo file_get_contents('resources/default_user.png');
}

$pdo = null;
?>