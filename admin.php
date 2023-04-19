<!DOCTYPE html>
<?php
session_start();

// Get outta here if you;re not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '<form id="redirect" method="post" action="login.php">
        <input type="hidden" name="redirect" value="admin.php">
    </form>
    <script>
        document.getElementById("redirect").submit();
    </script>';
    exit;
}

require_once("../constants.php");

$pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
$sql = "SELECT admin from user WHERE username = ?";
$statement = $pdo->prepare($sql);
$statement->execute([$_SESSION['username']]);
$result = $statement->fetch(PDO::FETCH_ASSOC);

// If not admin, redirect to index
if ($result['admin'] == 0) {
    header('Location: index.php');
    exit;
}

// Get reports
$sql = "SELECT reportId, reportedPost, reportedComment, type, reason, title, post.postId from Report left join post on Report.reportedPost = post.postId";
$statement = $pdo->prepare($sql);
$statement->execute();
$reports = $statement->fetchAll(PDO::FETCH_ASSOC);

// Get banned users
$sql = "SELECT username, banReason from user WHERE banned = true";
$statement = $pdo->prepare($sql);
$statement->execute();
$bannedUsers = $statement->fetchAll(PDO::FETCH_ASSOC);

// Get banned words
$sql = "SELECT word from BannedWords";
$statement = $pdo->prepare($sql);
$statement->execute();
$bannedWords = $statement->fetchAll(PDO::FETCH_ASSOC);

?>
<html>

<head>
    <title>Pans@Home - Lets get cooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/admin.css">
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
            </div>
        </div>
    </nav>
    <div id="content-div">
        <a href="stats.php" class="button-div"><div>View Graphs</div></a>
        <div id="columns">
            <div id="reports-col">
                <div class="col-header">
                    <h2>Reports</h2>
                </div>
                <div class="col-content">
                    <?php if (count($reports) == 0): ?>
                        <div class="filter-word" id="none">
                            <p>No pending reports</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($reports as $report): ?>
                        <?php
                        $type = $report['type']; 
                        if ($type == 'Comment') {
                            $reportedId = $report['reportedComment'];
                            $sql = "SELECT postId from comment WHERE commentId = ?";
                            $statement = $pdo->prepare($sql);
                            $statement->execute([$report['reportedComment']]);
                            $result = $statement->fetch(PDO::FETCH_ASSOC);
                            $postId = $result['postId'];
                        } else {
                            $reportedId = $report['reportedPost'];
                            $postId = $report['postId'];
                        }
                        ?>
                        <div reportId="<?= $report['reportId'] ?>" reportedId="<?= $reportedId ?>" reportType="<?= $type ?>" post="<?= $postId ?>" class="report">
                            <div class="left">
                                <h2><a href="post.php?id=<?= $postId ?>" target="_blank"><?= htmlentities($report['title'] ?? 'Comment#'.$report['reportedComment']) ?><img src="resources/external_link.svg"></a></h2>
                                <p>
                                    <?= htmlentities($report['reason']) ?>
                                </p>
                            </div>
                            <div class="right">
                                <img class="ban-icon" src="resources/trash.svg" title="Remove Post" alt="Remove Post" onclick="javascript:removePost(this)">
                                <img class="ban-icon" src="resources/x.svg" title="Ignore Report" alt="Ignore Report" onclick="javascript:ignoreReport(this)">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div id="bans-col">
                <div class="col-header">
                    <h2>Bans/Unbans</h2>
                </div>
                <div class="col-content">
                    <form id="ban-user" class="entry-box">
                        <div>
                            <input type="text" name="user" placeholder="Username" required>
                            <button type="submit">Ban</button>
                        </div>
                        <textarea type="textarea" name="reason" placeholder="Reason" required></textarea>
                    </form>
                    <?php if (count($bannedUsers) == 0): ?>
                        <div class="filter-word" id="none">
                            <p>No banned users</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($bannedUsers as $user): ?>
                        <div class="ban" user="<?= htmlentities($user['username']) ?>">
                            <div class="left">
                                <h2><a href="user.php?user=<?= htmlentities($user['username']) ?>">u/<?= htmlentities($user['username']) ?></a></h2>
                                <p>
                                    <?= htmlentities($user['banReason']) ?>
                                </p>
                            </div>
                            <img class="ban-icon" src="resources/unban.svg" title="Unban" alt="Unban" onclick="javascript:unban(this)">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div id="filter-col">
                <div class="col-header">
                    <h2>Word Filter</h2>
                </div>
                <div class="col-content">
                    <form id="ban-word" class="entry-box">
                        <div>
                            <input type="text" name="word" placeholder="Add word..." required>
                            <button type="submit">Add</button>
                        </div>
                    </form>
                    <?php if (count($bannedWords) == 0): ?>
                        <div class="filter-word" id="none">
                            <p>No banned words</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($bannedWords as $word): ?>
                        <div class="filter-word" word="<?= htmlentities($word['word']) ?>">
                            <p>
                                <?= htmlentities($word['word']) ?>
                            </p>
                            <img src="resources/x.svg" title="Remove word" alt="Remove word">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="js/admin.js"></script>
</body>

</html>