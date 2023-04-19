<?php
session_start();

// Check that all required fields are set
if (!isset($_POST['postId']) || !isset($_POST['comment'])) {
    echo 'missing fields';
    exit;
}

// Check if the user is already logged in, if yes then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo 'not logged in';
    exit;
} else {
    $username = $_SESSION['username'];
    $loggedInUser = true;
}

// Check that the comment is not empty or too long
if (strlen($_POST['comment']) == 0) {
    echo 'Comment cannot be empty';
    exit;
} else if (strlen($_POST['comment']) > 4095) {
    echo 'Comment cannot be longer than 4095 characters';
    exit;
}

require_once("../constants.php");

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);

    $sql = "update post set numComments = numComments + 1 where postId = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['postId']]);

    $sql = "insert into comment (postId, username, text, commentDate, upvotes, downvotes) values (?, ?, ?, ?, 0, 0)";
    $stmt = $pdo->prepare($sql);
    $now = new DateTime('now', new DateTimeZone('America/Vancouver'));
    $stmt->execute([$_POST['postId'], $username, $_POST['comment'], $now->format("Y-m-d H:i:s")]);

    echo 'success';

    $pdo = null;
} catch (PDOException $e) {
    echo "Connection failed:" . $e->getMessage();
}
?>