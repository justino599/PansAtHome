<?php

session_start();

// Check that a post is specified in the url
if (!isset($_POST['id']) || !isset($_POST['type']) || !($_POST['type'] == 'Post' || $_POST['type'] == 'Comment') || !isset($_POST['reason'])) {
    echo "Error: Invalid request";
    exit;
}

// Check that the user is logged in
if (!isset($_SESSION['username'])) {
    echo 'Not logged in';
    exit;
}

// Get post info from database
require_once("../constants.php");
try {
    $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);

    // Check that the reported object exists
    if ($_POST['type'] == 'Post') {
        $sql = "SELECT postId FROM post WHERE postId = ?";
    } else {
        $sql = "SELECT postId FROM comment WHERE commentId = ?";
    }

    $statement = $pdo->prepare($sql);
    $statement->execute([$_POST['id']]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo "Error: ".$_POST['type']." not found";
        exit;
    } else {
        $postId = $result['postId'];
    }

    // Check that the user has not already reported the post
    if ($_POST['type'] == 'Post') {
        $sql = "SELECT reportId FROM Report WHERE reportedPost = ? AND reportingUser = ?";
    } else {
        $sql = "SELECT reportId FROM Report WHERE reportedComment = ? AND reportingUser = ?";
    }

    $statement = $pdo->prepare($sql);
    $statement->execute([$_POST['id'], $_SESSION['username']]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "Error: You have already reported this";
        exit;
    }

    // Insert report into database
    if ($_POST['type'] == 'Post') {
        $sql = "INSERT INTO Report (reportingUser, reportedPost, type, reason) VALUES (?, ?, 'Post', ?)";
    } else {
        $sql = "INSERT INTO Report (reportingUser, reportedComment, type, reason) VALUES (?, ?, 'Comment', ?)";
    }

    $statement = $pdo->prepare($sql);
    $statement->execute([$_SESSION['username'], $_POST['id'], $_POST['reason']]);

    echo 'success,'.$postId;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>