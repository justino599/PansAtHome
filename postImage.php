<?php
require_once("../constants.php");

try {
    // Connect to the database
    $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    $pdo = new PDO($conString, DBUSER, DBPASS);

    // Check if the username is already taken or the email is already in use
    $sql = 'select postImage from post where postId = :id';
    $statement = $pdo->prepare($sql);

    $statement->bindValue(':id', $_GET['id']);

    $result = $statement->execute();

    if ($result) {
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            header('Content-Type: image/png');
            echo $row['postImage'];
        } else {
            header('Content-Type: image/png');
            echo file_get_contents('resources/wumpus.png');
        }
    } else {
        header('Content-Type: image/png');
        echo file_get_contents('resources/wumpus.png');
    }
} catch (PDOException $e) {
    header('Content-Type: image/png');
    echo file_get_contents('resources/wumpus.png');
}

$pdo = null;
?>