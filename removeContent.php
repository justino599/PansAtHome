<?php
session_start();

if (!isset($_POST['table']) || !isset($_POST['id'])) {
    echo 'Invalid request';
    exit;
}

$table = $_POST['table'];
$id = $_POST['id'];

require_once("../constants.php");

try {
    $pdo = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);

    if ($table == 'post') {
        $sql = "select username, upvotes, downvotes from post where postId = :id";
    } else if ($table == 'comment') {
        $sql = "select username, upvotes, downvotes from comment where commentId = :id";
    } else if ($table == 'report') {
        if (!($_SESSION['admin'] ?? false)) {
            echo 'You do not have permission to perform this action';
            exit;
        }
    } else {
        echo 'Invalid table';
        exit;
    }

    if ($table != 'report') {
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if (($result['username'] ?? '') != $_SESSION['username'] && !($_SESSION['admin'] ?? false)) {
                echo 'You do not have permission to perform this action';
                exit;
            }

            $sql = "update user set points = points - :points where username = :username";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':points', $result['upvotes'] - $result['downvotes']);
            $statement->bindValue(':username', $result['username']);
            $statement->execute();
        } else {
            echo 'Invalid id';
            exit;
        }
    }

    if ($table == 'post') {
        $sql = "delete from post where postId = :id";
    } else if ($table == 'comment') {
        $sql = "update post set numComments = numComments - 1 where postId = (select postId from comment where commentId = :id); delete from comment where commentId = :id;";
    } else if ($table == 'report') {
        $sql = "delete from Report where reportId = :id";
    }

    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $id);
    $result = $statement->execute();

    if ($result) {
        echo 'success';
    } else {
        echo 'failure';
    }

} catch (PDOException $e) {
    die($e->getMessage());
}

?>