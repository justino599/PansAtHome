<!DOCTYPE html>
<html>
<?php

session_start();

// Load all posts from database


?>

<head>
    <title>Pans@Home - Lets get cooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/index_page.css">
    <link rel="stylesheet" href="css/post.css">
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
        <div class="posts">
            <div class="post-filter">
                <div>
                    <img src="resources/rocket.svg" alt="Best">
                    <h2>Best</h2>
                </div>
                <div>
                    <img src="resources/top_arrow.svg" alt="Top">
                    <h2>Top</h2>
                </div>
                <div>
                    <img src="resources/new_star.svg" alt="New">
                    <h2>New</h2>
                </div>
            </div>
            <div class="create-post">
                <div onclick="location.href='new-post.php'">Create Post...</div>
                <a href="new-post.php"><img src="resources/picture.svg"></a>
            </div>
        </div>
        <div id="actually-the-posts" class="posts">
        </div>
    </div>
    <input type="hidden" id="filter" value="best">
    <script src="js/loadHomePosts.js"></script>
    <script>
        $(document).ready(function () {
            $('.post-filter div').click(function () {
                $('.post-filter div').removeClass('active');
                $(this).addClass('active');
                $('#filter').val($(this).children('h2').text().toLowerCase());
                loadPosts();
            });
        });
    </script>
</body>

</html>