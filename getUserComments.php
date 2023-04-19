<?php
session_start();
// Check that a post id is set
if (!isset($_GET['user'])) {
    echo 'missing fields';
    exit;
}

// Check if there is a search filter
if (isset($_GET['search'])) {
    $search = $_GET['search'];
} else {
    $search = '';
}

if (isset($_SESSION["username"])) {
    $loggedIn = true;
    $loggedInuser = $_SESSION["username"];
} else {
    $loggedIn = false;
}

require_once("../constants.php");

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);

    $sql = "select * from comment where username = :id and (text like :search or username like :search) order by commentDate desc";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_GET['user'], ':search' => '%' . $search . '%']);

    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($comments as $comment) {
        $commentId = $comment["commentId"];
        $text = $comment["text"];
        $upvotes = $comment["upvotes"];
        $downvotes = $comment["downvotes"];
        $username = $comment["username"];
        $commentDate = $comment["commentDate"];
        $postId = $comment["postId"];

        $now = new DateTime('now', new DateTimeZone('America/Vancouver'));
        $then = new DateTime($commentDate, new DateTimeZone('America/Vancouver'));
        $timeSince = $now->getTimestamp() - $then->getTimestamp();

        if ($timeSince < 60) {
            $timeSince = "Now";
        } else if ($timeSince < 3600) {
            $timeSince = floor($timeSince / 60) . " mins ago";
        } else if ($timeSince < 86400) {
            $timeSince = floor($timeSince / 3600) . " hours ago";
        } else if ($timeSince < 604800) {
            $timeSince = floor($timeSince / 86400) . " days ago";
        } else if ($timeSince < 2419200) {
            $timeSince = floor($timeSince / 604800) . " weeks ago";
        } else if ($timeSince < 29030400) {
            $timeSince = floor($timeSince / 2419200) . " months ago";
        } else {
            $timeSince = floor($timeSince / 29030400) . " years ago";
        }

        // Check if the logged in user has upvoted this comment
        if ($loggedIn) {
            $sql = "select upvoted, downvoted from UserVotedComment u where commentId = :id and username = :loggedinuser";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute(["id" => $commentId, "loggedinuser" => $loggedInuser]);
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

        echo '
        <div class="comment" id="' . htmlentities($commentId) . '" postId="'. $postId .'" style="display: none;">
            <!--<div class="pfp-line">
                <a href="user.php?user='.htmlentities($username).'">
                    <img src="userPfp.php?user='.htmlentities($username).'" alt="u/'.htmlentities($username).'">
                </a>
                <div></div>
            </div> -->
            <div>
                <div class="this-comment">
                    <div class="post-top-info">
                        <p><a href="user.php?user='.htmlentities($username).'">u/'.htmlentities($username).'</a></p>
                        <img src="resources/clockIcon.svg">
                        <p>'.$timeSince.'</p>
                    </div>
                    <p class="comment-text">'.htmlentities($text).'</p>
                    <div class="post-toolbar">
                        <div class="votes">
                            <img src="'.($upvoted ? 'resources/ChefKnifeUp.svg' : 'resources/ChefKnife.svg').'" class="upvote">
                            <div>'.($upvotes - $downvotes).'</div>
                            <img src="'.($downvoted ? 'resources/ChefKnifeDown.svg' : 'resources/ChefKnife.svg').'" class="downvote">
                        </div>
                        <div class="share">
                            <img src="resources/share.png">
                            <p>Share</p>
                        </div>
                        <div>
                            <img src="resources/reportIcon.svg">
                            <p><a class="no-underline" href="report.php?type=Comment&id='.$commentId.'">Report</a></p>
                        </div>'.($username == ($_SESSION['username'] ?? '') ? '
                        <div>
                            <img src="resources/edit.svg">
                            <p>Edit</p>
                        </div>' : '').'
                    </div>
                </div>
            </div>
        </div>';
    }
} catch (PDOException $e) {
    echo "Connection failed:" . $e->getMessage();
}

?>