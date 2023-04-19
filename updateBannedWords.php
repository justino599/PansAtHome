<?php

session_start();

if (!($_SESSION['admin'] ?? false)) {
    echo 'You do not have permission to perform this action';
    exit;
}

if (!isset($_POST['action']) || !isset($_POST['word'])) {
    echo 'Invalid request';
    exit;
}

$action = $_POST['action'];
$word = $_POST['word'];

require_once("../constants.php");

try {
    $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    $pdo = new PDO($conString, DBUSER, DBPASS);

    if ($action == 'add') {
        $sql = 'insert into BannedWords (word) values (:word)';
    } else if ($action == 'remove') {
        $sql = 'delete from BannedWords where word = :word';
    } else {
        echo 'Invalid action';
        exit;
    }

    $statement = $pdo->prepare($sql);
    $statement->bindValue(':word', $word);
    $result = $statement->execute();

    // Update existing posts and comments to censor the newly banned word
    if ($action == 'add') {
        $sql = 'update post set text = replace(text, :word, "****"), title = replace(title, :word, "****")';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':word', $word);
        $statement->execute();

        $sql = 'update comment set text = replace(text, :word, "****")';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':word', $word);
        $statement->execute();
    }


    if ($result) {
        echo 'success';
    } else {
        echo 'failure';
    }
} catch (PDOException $e) {
    die($e->getMessage());
}

?>