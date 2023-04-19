<!DOCTYPE html>
<html>

<?php
session_start();

// Check that a post ID was passed
if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

if (isset($_SESSION["username"])) {
    $loggedIn = true;
    $loggedInuser = $_SESSION["username"];
} else {
    $loggedIn = false;
}

require_once("../constants.php");

$pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
$sql = "SELECT * FROM post WHERE postId = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$_GET['id']]);

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if ($post == null) {
    header("Location: home.php");
    exit();
}

// Get post info
$postId = $post['postId'];
$title = $post['title'];
$text = $post['text'];
$numComments = $post['numComments'];
$upvotes = $post['upvotes'];
$downvotes = $post['downvotes'];
$username = $post['username'];
$postDate = $post['postDate'];

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

?>

<head>
    <title>Pans@Home - Lets get cooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/post.css">
    <link rel="stylesheet" href="css/comment_page.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<link rel="shortcut icon" type="image/jpg" href="resources/favicon.ico"/>
</head>

<body>
<nav>
        <div class="navbar">
            <div class="desktop">
                <div id="logo">
                    <a href="home.php">
                        <img src="resources/logo_name_white.svg" alt="Pans@Home Logo">
                    </a>
                </div>
                <div id="search">
                    <form method="GET" action="home.php">
                        <input type="text" name="search" placeholder="Search..." required>
                    </form>
                </div>
                <div id="right-nav">
                    <div id="new-post">
                        <a href="new-post.php">
                            <img src="resources/new_post.svg" alt="New Post">
                        </a>
                    </div>
                    <div id="settings">
                        <a href="settings.php">
                            <img src="resources/cog.svg" alt="Settings">
                        </a>
                    </div>
                    <div id="profile">
                        <a href="user.php">
                            <?php if (isset($_SESSION['username'])): ?>
                                <img src="userPfp.php?user=<?= $_SESSION['username'] ?>" alt="Profile">
                            <?php else: ?>
                                <img src="resources/default_user.svg" alt="Profile">
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mobile">
                <div id="top">
                    <div id="logo">
                        <a href="home.php">
                            <img src="resources/logo_name_white.svg" alt="Pans@Home Logo">
                        </a>
                    </div>
                    <div id="right-nav">
                        <div id="new-post">
                            <a href="new-post.php">
                                <img src="resources/new_post.svg" alt="New Post">
                            </a>
                        </div>
                        <div id="settings">
                            <a href="settings.php">
                                <img src="resources/cog.svg" alt="Settings">
                            </a>
                        </div>
                        <div id="profile">
                            <a href="user.php">
                                <?php if (isset($_SESSION['username'])): ?>
                                    <img src="userPfp.php?user=<?= $_SESSION['username'] ?>" alt="Profile">
                                <?php else: ?>
                                    <img src="resources/default_user.svg" alt="Profile">
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="search">
                    <form method="GET" action="home.php">
                        <input type="text" name="search" placeholder="Search..." required>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <div class="content-div">
        <div id="post-and-comments">
            <div class="post" id="<?= $postId ?>">
                <div class="votes">
                    <img src="<?= ($upvoted ? 'resources/ChefKnifeUp.svg' : 'resources/ChefKnife.svg') ?>" class="upvote">
                    <div>
                        <?= $upvotes - $downvotes ?>
                    </div>
                    <img src="<?= ($downvoted ? 'resources/ChefKnifeDown.svg' : 'resources/ChefKnife.svg') ?>" class="downvote">
                </div>
                <div class="post-content">
                    <div class="post-top-info">
                        <p>Posted by <a href="user.php?user=<?= htmlentities($username) ?>">u/<?php echo htmlentities($username); ?></a></p>
                        <img src="resources/clockIcon.svg">
                        <p>
                            <?= $timeSince ?>
                        </p>
                    </div>
                    <h2>
                        <?php echo htmlentities($title); ?>
                    </h2>
                    <?php if (isset($text)): ?>
                        <?php if (strlen($text) > 0): ?>
                            <p class="post-text">
                                <?php echo htmlentities($text); ?>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <figure>
                            <img src="postImage.php?id=<?= $postId ?>">
                        </figure>
                    <?php endif; ?>
                    <div class="post-toolbar">
                        <div>
                            <img src="resources/commentIcon.svg">
                            <p>
                                <?= $numComments ?> Comments
                            </p>
                        </div>
                        <div class="share">
                            <img src="resources/share.png">
                            <p>Share</p>
                        </div>
                        <div>
                            <img src="resources/reportIcon.svg">
                            <p><a class="no-underline" href="report.php?type=Post&id=<?= $postId ?>">Report</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <form id="write-comment">
                <textarea id="comment-input" type="text" name="comment" placeholder="What are your thoughts?" oninput="autoResize(this)"
                    required></textarea>
                <input type="hidden" name="postId" value="<?= $postId ?>">
                <button type="submit">Comment</button>
            </form>
            <p class="error" id="comment-error"></p>
            <script>
                function autoResize(textarea) {
                    textarea.style.height = "auto";
                    textarea.style.height = (textarea.scrollHeight + 10) + "px";
                }
                $(document).ready(function () {
                    $("#write-comment").submit(function (e) {
                        e.preventDefault();

                        // Check if the user is banned
                        $.ajax({
                            url: 'isBanned.php?username=<?= $_SESSION['username'] ?>',
                            type: 'GET',
                            success: function (data) {
                                if (data == 'true') {
                                    alert('You are banned from commenting');
                                } else {
                                    var formData = new FormData();
                                    formData.append("data", $("#comment-input").val());
                                    var xhr = new XMLHttpRequest();
                                    xhr.open("POST", "censor.php", false);
                                    xhr.send(formData);
                                    $("#comment-input").val(xhr.responseText);

                                    console.log($("#write-comment").serialize());

                                    $.ajax({
                                        type: "POST",
                                        url: "addComment.php",
                                        data: $("#write-comment").serialize(),
                                        success: function (response) {
                                            if (response == "not logged in") {
                                                // Write a form that will redirect here
                                                $("#write-comment").after("<form id='login-redirect' action='login.php' method='POST'><input type='hidden' name='redirect' value='post.php?id=<?= $postId ?>'></form>");
                                                $("#login-redirect").submit();
                                            } else if (response == "success") {
                                                $("#write-comment textarea").val("");
                                                $("#write-comment textarea").css("height", "auto");
                                                loadComments();
                                            } else {
                                                $("#comment-error").text(response);
                                            }
                                        }
                                    });
                                }
                            }
                        });

                    });
                });
            </script>
            <div id="comments">
            </div>
        </div>
    </div>
    <input type="hidden" id="postId" value="<?= $postId ?>">
    <script src="js/post.js"></script>
</body>
<html>