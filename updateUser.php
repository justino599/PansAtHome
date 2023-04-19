<?php
require_once("../constants.php");
session_start();

$username = $_POST['username'];
// Check if the user is logged in
if (!(isset($_SESSION['username']) || $_SESSION['username'] == $username || $_SESSION['admin'] ?? false)) {
    echo 'You do not have permission to perform this action';
    exit;
}
$what = $_POST['what'];
if ($what == 'pfp') {
    $to = file_get_contents($_FILES['to']['tmp_name']);
} else {
    $to = $_POST['to'];
}

try {
    // Connect to the database
    $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    $pdo = new PDO($conString, DBUSER, DBPASS);

    if ($what == 'pfp')
        $sql = "update user set pfp = :to where username = :username";
    else if ($what == 'username')
        $sql = "update user set username = :to where username = :username";
    else if ($what == 'email')
        $sql = "update user set email = :to where username = :username";
    else if ($what == 'password') {
        $sql = "update user set password = :to where username = :username";
        $options = [
            'cost' => 9,
        ];
        $to = password_hash($to, PASSWORD_BCRYPT, $options);
    }
    else if ($what == 'points')
        $sql = "update user set points = :to where username = :username";
    else if ($what == 'admin')
        $sql = "update user set admin = :to where username = :username";
    else if ($what == 'banned')
        $sql = "update user set banned = :to where username = :username";
    else if ($what == 'banReason')
        $sql = "update user set banReason = :to where username = :username";
    else {
        echo 'No such field "'. $what . '" exists';
        exit;
    }

    $statement = $pdo->prepare($sql);

    $statement->bindValue(':to', $to);
    $statement->bindValue(':username', $username);

    $result = $statement->execute();

    if ($result) {
        echo 'success';
    } else {
        echo 'failure';
    }

    $pdo = null;
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>