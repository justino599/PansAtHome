<!DOCTYPE html>

<?php

// Start the session
session_start();

// Check if the user is already logged in, if yes then redirect them to home page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
}

?>

<html>

<head>
    <title>Pans@Home Sign in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/login_page.css">
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
        <?php if (isset($_GET['createAccount'])): ?>
            <form id="register" class="login-Screen" enctype="multipart/form-data" method="post" action="">
                <fieldset>
                    <h1>Create Account</h1>
                    <p id="login-error" class="error"></p>
                    <p><input type="username" name="username" id="username" placeholder="Username" required></p>
                    <p><input type="password" name="password" placeholder="Password" required></p>
                    <p><input type="password" name="passwordconfirm" placeholder="Confirm Password" required></p>
                    <p><input type="email" name="email" placeholder="Email" required></p>
                    <p id="pfpUpload"><label for="pfp">Profile Picture </label><input type="file" accept=".png,.jpg,.jpeg"
                            name="pfp" id="pfp"></p>
                    <p><input id="login-button" type="submit" value="Create Account"></p>
                    <p>Already have an account? <a href="login.php">Log In</a></p>
                </fieldset>
            </form>
        <?php else: ?>
            <form id="login" class="login-Screen">
                <fieldset>
                    <h1>Log In</h1>
                    <p id="login-error" class="error"></p>
                    <p><input type="username" name="username" placeholder="Username" required></p>
                    <p><input type="password" name="password" placeholder="Password" required></p>
                    <!-- <a href="forgotPassword.php" id="forgot">Forgot password?</a> -->
                    <p><input id="login-button" type="submit" value="Sign In"></p>
                    <p id="signup">New to Pans? <a href="?createAccount=true">Sign up</a></p>
                </fieldset>
            </form>
        <?php endif ?>
    </div>
    <input id="redirect" type="hidden" value="<?php echo $_POST['redirect'] ?? 'home.php'; ?>">
    <script src="js/login.js"></script>
</body>
</html>