<!DOCTYPE html>
<html>

<?php

session_start();
// Check if the user is already logged in, if yes then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Redirect to login page and set redirct to settings in post
    echo '<form id="redirect" method="post" action="login.php">
            <input type="hidden" name="redirect" value="settings.php">
        </form>
        <script>
            document.getElementById("redirect").submit();
        </script>';
    exit;
}

// Get user info from database
require_once("../constants.php");
try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);

    $sql = "SELECT username, email, points, joinDate FROM user WHERE username = ?";
    $statement = $pdo->prepare($sql);

    $statement->execute([$_SESSION["username"]]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $username = $result["username"];
        $email = $result["email"];
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
    <link rel="stylesheet" href="css/user_settings_page.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/dot-luv/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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
        <div class="col2">
            <div id="user-settings">
                <h1>Account Settings</h1>
                <div class="setting-group">
                    <div>
                        <h2>Email Address</h2>
                        <p id="email">
                            <?= $email ?>
                        </p>
                    </div>
                    <div id="change-email" class="setting-button">Change</div>
                </div>
                <div class="setting-group">
                    <div>
                        <h2>Change Password</h2>
                        <p>Password must have at least 8 characters, one uppercase and lowercase, one number, and one
                            symbol</p>
                    </div>
                    <div id="change-password" class="setting-button">Change</div>
                </div>
                <div class="setting-group">
                    <div>
                        <h2>Profile Picture</h2>
                        <figure>
                            <img class="profile" src="userPfp.php?user=<?= $username ?>">
                        </figure>
                    </div>
                    <div id="change-pfp" class="setting-button">Change</div>
                </div>
                <div class="setting-group">
                    <div>
                        <h2>Delete Account</h2>
                        <p id="delete"><a href="javascript:deleteAccount()">PERMANENTLY DELETE ACCOUNT</a> </p>
                    </div>
                </div>
            </div>
            <div id="user-block">
                <figure>
                    <img class="profile" src="userPfp.php?user=<?= $username ?>">
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
                <a id="logout" href="">
                    <div>Log Out</div>
                </a>
                <script>
                    $("#logout").click(function (e) {
                        e.preventDefault();
                        window.location.href = "logout.php";
                    });
                </script>
            </div>
        </div>
    </div>
    <!-- Dialogs -->
    <div id="email-dialog" class="dialog" title="Change Email">
        <form>
            <p id="email-error" class="error"></p>
            <input type="text" name="email" id="email" placeholder="Email">
            <input type="email" name="emailConfirm" id="emailConfirm" placeholder="Confirm Email">
            <button type="submit" id="email-submit">Change Email</button>
        </form>
    </div>
    <div id="password-dialog" class="dialog" title="Change Password">
        <form>
            <p id="password-error" class="error"></p>
            <input type="password" name="oldPassword" id="oldPassword" placeholder="Current Password">
            <input type="password" name="newPassword" id="password" placeholder="New Password">
            <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirm Password">
            <button type="submit" id="password-submit">Change Password</button>
        </form>
    </div>
    <div id="pfp-dialog" class="dialog" title="Change Profile Picture">
        <form enctype="multipart/form-data" method="post">
            <p id="pfp-error" class="error"></p>
            <input type="file" accept=".png,.jpg,.jpeg" name="pfp" id="pfp">
            <button type="submit" id="pfp-submit">Change Profile Picture</button>
        </form>
    </div>

    <input type="hidden" id="username" value="<?= $username ?>">
    <script src="js/settings.js"></script>
</body>

</html>