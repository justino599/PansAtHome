<!DOCTYPE html>
<?php

// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!(!empty($_SESSION['username']) && $_SESSION['admin'] ?? false)) {
    header('Location: login.php');
    exit;
}

?>

<html>

<head>
    <title>Pans@Home - Lets get cooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/stats.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <div id="graphs">
            <div class="graph" id="daily-activity">
                <div>
                    <select id="daily-activity-select">
                        <option value="0">Posts/day</option>
                        <option value="1">Comments/day</option>
                    </select>
                    <select id="daily-activity-time-select">
                        <option value="1">Today</option>
                        <option value="7" selected>Last week</option>
                        <option value="30">Last month</option>
                        <option value="365">Last year</option>
                        <option value="100000">All time</option>
                    </select>
                </div>
                <p class="error" id="daily-activity-error"></p>
                <canvas id="daily-activity-canvas"></canvas>
            </div>
            <div class="graph" id="post-activity">
                <div>
                    <select id="post-activity-select">
                        <option value="2">Comments/post</option>
                        <option value="3">Votes/post</option>
                    </select>
                    <select id="post-activity-time-select">
                        <option value="1">Today</option>
                        <option value="7" selected>Last week</option>
                        <option value="30">Last month</option>
                        <option value="365">Last year</option>
                        <option value="100000">All time</option>
                    </select>
                </div>
                <p class="error" id="post-activity-error"></p>
                <canvas id="post-activity-canvas"></canvas>
            </div>
            <div class="graph" id="interactions">
                <p>Interactions/day
                    <select id="interactions-select">
                        <option value="1">Today</option>
                        <option value="7" selected>Last week</option>
                        <option value="30">Last month</option>
                        <option value="365">Last year</option>
                        <option value="100000">All time</option>
                    </select>
                </p>
                <p class="error" id="interactions-error"></p>
                <canvas id="interactions-canvas"></canvas>
            </div>
            <div class="graph" id="active-users">
                <p>Active users
                    <select id="active-users-select">
                        <option value="1">Today</option>
                        <option value="7" selected>Last week</option>
                        <option value="30">Last month</option>
                        <option value="365">Last year</option>
                        <option value="100000">All time</option>
                    </select>
                </p>
                <p class="error" id="active-users-error"></p>
                <ul id="active-users-list"></ul>
            </div>
            <div class="graph" id="post-type">
                <p>Post Type
                    <select id="post-type-select">
                        <option value="1">Today</option>
                        <option value="7" selected>Last week</option>
                        <option value="30">Last month</option>
                        <option value="365">Last year</option>
                        <option value="100000">All time</option>
                    </select>
                </p>
                <p class="error" id="post-type-error"></p>
                <canvas id="post-type-canvas"></canvas>
            </div>
            <div class="graph" id="popular-hours">
                <p>Popular hours
                    <select id="popular-hours-select">
                        <option value="1">Today</option>
                        <option value="7" selected>Last week</option>
                        <option value="30">Last month</option>
                        <option value="365">Last year</option>
                        <option value="100000">All time</option>
                    </select>
                </p>
                <p class="error" id="popular-hours-error"></p>
                <canvas id="popular-hours-canvas" height=""></canvas>
            </div>
        </div>
    </div>
    <script src="js/stats.js"></script>
</body>

</html>