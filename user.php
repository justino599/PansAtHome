<!DOCTYPE html>
<html>

<?php
session_start();

// If a different user is specified in the url
if (isset($_GET['user'])) {
    $username = $_GET['user'];
    $loggedInUser = false;
} else {
    // Else use the current user
    // Check if the user is already logged in, if yes then redirect them to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        echo '<form id="redirect" method="post" action="login.php">
            <input type="hidden" name="redirect" value="user.php">
        </form>
        <script>
            document.getElementById("redirect").submit();
        </script>';
        exit;
    } else {
        $username = $_SESSION['username'];
        $loggedInUser = true;
    }
}

// Get user info from database
require_once("../constants.php");
try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);

    $sql = "SELECT username, points, joinDate FROM user WHERE username = ?";
    $statement = $pdo->prepare($sql);

    $statement->execute([$username]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $username = $result["username"];
        $points = $result["points"];
        $date = new DateTimeImmutable($result["joinDate"]);
        $joinDate = $date->format('F j, Y');
    } else {
        echo "Error: User not found";
        exit;
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>

<head>
    <title>Pans@Home - Lets get cooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/post.css">
    <link rel="stylesheet" href="css/user.css">
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
    <div id="content-div">
        <div class="posts">
            <div class="post-filter">
                <div class="active">
                    <img src="resources/postIcon.png" alt="Posts">
                    <h2>Posts</h2>
                </div>
                <div>
                    <img src="resources/commentIcon.svg" alt="Comments">
                    <h2>Comments</h2>
                </div>
            </div>
            <script>
                $(".post-filter>div").click(function () {
                    $(".post-filter>div").removeClass("active");
                    $(this).addClass("active");

                    var type = $(this).find("h2").text();

                    if (type == "Posts") {
                        $(".post").show();
                        $(".comment").hide();
                    } else {
                        $(".post").hide();
                        $(".comment").show();
                    }
                });
            </script>
        </div>
        <div id="user-block">
            <figure>
                <img src="userPfp.php?user=<?= htmlentities($username) ?>">
                <figcaption>u/
                    <?= htmlentities($username) ?>
                </figcaption>
            </figure>
            <div class="line"></div>
            <div class="info">
                <p>Brownie Points</p>
                <p>
                    <?= $points ?>
                </p>
            </div>
            <div class="info">
                <p>Day Joined</p>
                <p>
                    <?= $joinDate ?>
                </p>
            </div>
            <?php if ($loggedInUser): ?>
                <a href="" id="logout">
                    <div>Log Out</div>
                </a>
                <script>
                    $("#logout").click(function (e) {
                        e.preventDefault();
                        window.location.href = "logout.php";
                    });
                </script>
            <?php endif; ?>
            <?php if ($loggedInUser && ($_SESSION['admin'] ?? false)): ?>
                <a href="admin.php" id="admin">
                    <div>Admin Panel</div>
                </a>
                <script>
                    $("#admin").click(function (e) {
                        e.preventDefault();
                        window.location.href = "admin.php";
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
    <input type="hidden" id="username" value="<?= htmlentities($username) ?>">
    <script src="js/loadUserPosts.js"></script>
</body>

</html>