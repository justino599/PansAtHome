<?php
session_start();

// Determin post filter
if (isset($_GET["filter"])) {
    $filter = $_GET["filter"];
} else {
    $filter = "best";
}

// Check if we only want one user's posts
if (isset($_GET["user"])) {
    $user = $_GET["user"];
} else {
    $user = "";
}

// Cheack if we are searching for a specific post
if (isset($_GET["search"])) {
    $search = $_GET["search"];
} else {
    $search = "";
}

if (isset($_SESSION["username"])) {
    $loggedIn = true;
    $loggedInuser = $_SESSION["username"];
} else {
    $loggedIn = false;
}

// Load all posts from database
require_once("../constants.php");

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
    if ($user != "") {
        $sql = "SELECT * FROM post WHERE username = :username AND (title LIKE :search OR text LIKE :search OR username LIKE :search) ORDER BY postDate DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["username" => $user, "search" => "%" . $search . "%"]);
    } else {
        $sql = "SELECT * FROM post WHERE title LIKE :search OR text LIKE :search OR username LIKE :search";
        if ($filter == "new") {
            $sql = $sql . " ORDER BY postDate DESC";
        } else if ($filter == "top") {
            $sql = $sql . " ORDER BY (upvotes - downvotes) DESC";
        } else {
            $sql = $sql . " ORDER BY (upvotes - downvotes) / (TIMESTAMPDIFF(HOUR, postDate, NOW()) + 2) DESC";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["search" => "%" . $search . "%"]);
    }
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display posts
    foreach ($posts as $post) {
        $postId = $post["postId"];
        $title = $post["title"];
        $text = $post["text"];
        $numComments = $post["numComments"];
        $upvotes = $post["upvotes"];
        $downvotes = $post["downvotes"];
        $username = $post["username"];
        $postDate = $post["postDate"];

        $now = new DateTime('now', new DateTimeZone('America/Vancouver'));
        $then = new DateTime($postDate, new DateTimeZone('America/Vancouver'));
        $timeSince = $now->getTimestamp() - $then->getTimestamp();

        if ($timeSince < 60) {
            $timeSince = "Now";
        } else if ($timeSince < 3600) {
            $timeSince = floor($timeSince / 60) . " mins ago";
        } else if ($timeSince < 86400) {
            $timeSince = floor($timeSince / 3600) . " hours ago";
        } else if ($timeSince < 604800) {
            $timeSince = floor($timeSince / 86400) . " days ago";
        } else if ($timeSince < 2629743) {
            $timeSince = floor($timeSince / 604800) . " weeks ago";
        } else if ($timeSince < 31556926) {
            $timeSince = floor($timeSince / 2629743) . " months ago";
        } else {
            $timeSince = floor($timeSince / 31556926) . " years ago";
        }

        // Check if the logged in user has upvoted this post
        if ($loggedIn) {
            $sql = "select upvoted, downvoted from UserVotedPost u where postId = :id and username = :loggedinuser";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute(["id" => $postId, "loggedinuser" => $loggedInuser]);
            if ($success) {
                $userVoted = $stmt->fetch(PDO::FETCH_ASSOC);
                $upvoted = $userVoted["upvoted"] ?? false;
                $downvoted = $userVoted["downvoted"] ?? false;
            } else {
                $upvoted = false;
                $downvoted = false;
            }
        } else {
            $upvoted = false;
            $downvoted = false;
        }

        if ($post['postImage'] != null) {
            echo '
                <div class="post" id="' . htmlentities($postId) . '">
                    <div class="votes">
                        <img src="'.($upvoted ? 'resources/ChefKnifeUp.svg' : 'resources/ChefKnife.svg').'" class="upvote">
                        <div>' . ($upvotes - $downvotes) . '</div>
                        <img src="'.($downvoted ? 'resources/ChefKnifeDown.svg' : 'resources/ChefKnife.svg').'" class="downvote">
                    </div>
                    <div class="post-content">
                        <div class="post-top-info">
                            <p>Posted by <a href="user.php?user=' . htmlentities($username) . '">u/' . htmlentities($username) . '</a></p>
                            <img src="resources/clockIcon.svg">
                            <p>' . $timeSince . '</p>
                        </div>
                        <a class="no-underline" href="post.php?id=' . $postId . '"><h2>' . htmlentities($title) . '</h2></a>
                        <figure>
                            <img src="postImage.php?id=' . $postId . '" alt="' . htmlentities($title) . '">
                        </figure>
                        <div class="post-toolbar">
                            <a class="no-underline" href="post.php?id=' . $postId . '">
                                <div>
                                    <img src="resources/commentIcon.svg">
                                    <p>' . $numComments . ' Comments</p>
                                </div>
                            </a>
                            <div class="share">
                                <img src="resources/share.png">
                                <p>Share</p>
                            </div>
                            <div>
                                <img src="resources/reportIcon.svg">
                                <p><a class="no-underline" href="report.php?type=Post&id='.$postId.'">Report</a></p>
                            </div>
                        </div>
                    </div>
                </div>';
        } else {
            echo '
                <div class="post" id="' . htmlentities($postId) . '">
                    <div class="votes">
                        <img src="'.($upvoted ? 'resources/ChefKnifeUp.svg' : 'resources/ChefKnife.svg').'" class="upvote">
                        <div>' . ($upvotes - $downvotes) . '</div>
                        <img src="'.($downvoted ? 'resources/ChefKnifeDown.svg' : 'resources/ChefKnife.svg').'" class="downvote">
                    </div>
                    <div class="post-content">
                        <div class="post-top-info">
                            <p>Posted by <a href="user.php?user=' . htmlentities($username) . '">u/' . htmlentities($username) . '</a></p>
                            <img src="resources/clockIcon.svg">
                            <p>' . $timeSince . '</p>
                        </div>
                        <a class="no-underline" href="post.php?id=' . $postId . '"><h2>' . htmlentities($title) . '</h2></a>
                        ' . ($text != "" ? '<p class="post-text">' . htmlentities($text) . '</p>' : '') . '
                        <div class="post-toolbar">
                            <a class="no-underline" href="post.php?id=' . $postId . '">
                                <div>
                                    <img src="resources/commentIcon.svg">
                                    <p>' . $numComments . ' Comments</p>
                                </div>
                            </a>
                            <div class="share">
                                <img src="resources/share.png">
                                <p>Share</p>
                            </div>
                            <div>
                                <img src="resources/reportIcon.svg">
                                <p><a class="no-underline" href="report.php?type=Post&id='.$postId.'">Report</a></p>
                            </div>
                        </div>
                    </div>
                </div>';
        }
    }
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

?>