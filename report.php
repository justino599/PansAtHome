<!DOCTYPE html>
<?php

session_start();

// Check that a post is specified in the url
if (!isset($_GET['type']) || !($_GET['type'] == 'Post' || $_GET['type'] == 'Comment') || !isset($_GET['id'])) {
    header('LOcation: ' . ($_SERVER['HTTP_REFERER'] ?? 'home.php'));
    exit;
}

// Check that the user is logged in
if (!isset($_SESSION['username'])) {
    echo '<form id="redirect" method="post" action="login.php">
            <input type="hidden" name="redirect" value="' . ($_SERVER['REQUEST_URI'] ?? 'home.php') . '">
        </form>
        <script>
            document.getElementById("redirect").submit();
        </script>';
    exit;
}

?>

<html>

<head>
    <title>Pans@Home Sign in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/report.css">
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
        <form id="report" method="post" action="submitReport.php">
            <h1>Report <?= $_GET['type'] ?></h1>
            <p id="report-error" class="error"></p>
            <textarea type="text" name="reason" placeholder="Reason" required></textarea>
            <input type="hidden" name="type" value="<?= $_GET['type'] ?>">
            <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
            <input id="submit" type="submit" value="Submit">
        </form>
    </div>
    <script src="js/report.js"></script>
</body>

</html>