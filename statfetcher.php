<?php

// Admins only
// if (!($_SESSION['admin'] ?? false)) {
//     echo '{"success": 0, "response": "You do not have permission to perform this action"}';
//     exit();
// }

// Check that there was a query given
$query = $_POST['query'] ?? -1;
if ($query < 0 || $query > 7) {
    echo '{"success": 0, "response": "Not a valid query"}';
    exit();
}

try {
    // Connect to the database
    require_once("../constants.php");

    $pdo = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);

    // Run the query
    if ($query == 0) {
        // Posts per day
        $sql = "SELECT DATE(postDate) AS date, COUNT(*) AS count FROM post WHERE postDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) GROUP BY DATE(postDate) ORDER BY date";
        $modifier = $_POST['modifier'] ?? 7;
    } else if ($query == 1) {
        // Comments per day
        $sql = "SELECT DATE(commentDate) AS date, COUNT(*) AS count FROM comment WHERE commentDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) GROUP BY DATE(commentDate) ORDER BY date";
        $modifier = $_POST['modifier'] ?? 7;
    } else if ($query == 2) {
        // Comments per post
        $sql = "SELECT COUNT(*) AS count FROM comment WHERE commentDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) GROUP BY postID ORDER BY count DESC";
        $modifier = $_POST['modifier'] ?? 7;
    } else if ($query == 3) {
        // Votes per post
        $sql = "SELECT (upvotes - downvotes) AS count FROM post WHERE postDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) GROUP BY postID ORDER BY count DESC";
        $modifier = $_POST['modifier'] ?? 7;
    } else if ($query == 4) {
        // Total interactions per day
        $sql = "SELECT DATE(interactionDate) AS date, COUNT(*) AS count FROM (SELECT postDate AS interactionDate FROM post WHERE postDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) UNION ALL SELECT commentDate AS interactionDate FROM comment WHERE commentDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY)) r GROUP BY date ORDER BY date";
        $modifier = $_POST['modifier'] ?? 7;
    } else if ($query == 5) {
        // Active users
        $sql = "SELECT username FROM post WHERE postDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) UNION SELECT username FROM comment WHERE commentDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY)";
        $modifier = $_POST['modifier'] ?? 7;
    } else if ($query == 6) {
        // Text / image ratio
        $sql = "SELECT (SELECT COUNT(*) FROM post WHERE postImage IS NOT NULL AND postDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY)) / (SELECT COUNT(*) FROM post WHERE postDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY)) AS textRatio";
        $modifier = $_POST['modifier'] ?? 7;
    } else if ($query == 7) {
        // Which hour of the day has the most posts/comments
        $sql = "SELECT hour, SUM(count) AS count FROM (SELECT HOUR(postDate) AS hour, COUNT(*) AS count FROM post WHERE postDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) GROUP BY hour UNION ALL SELECT HOUR(commentDate) AS hour, COUNT(*) AS count FROM comment WHERE commentDate >= DATE_SUB(NOW(), INTERVAL :modifier DAY) GROUP BY hour) r GROUP BY hour ORDER BY hour";
        $modifier = $_POST['modifier'] ?? 7;
    }

    $statement = $pdo->prepare($sql);
    if (isset($modifier))
        $statement->bindValue(':modifier', $modifier, PDO::PARAM_INT);
    $success = $statement->execute();

    if (!$success) {
        echo '{"success": 0, "response": "Query failed"}';
        exit();
    }

    // Return the results
    echo '{"success": 1, "response": ' . json_encode($statement->fetchAll(PDO::FETCH_ASSOC)) . '}';
    exit();
} catch (PDOException $e) {
    echo '{"success": 0, "response": "' . $e->getMessage() . '"}';
    exit();
}
?>