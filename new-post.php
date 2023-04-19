<!DOCTYPE html>
<html>

<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo '<form id="redirect" method="post" action="login.php">
        <input type="hidden" name="redirect" value="new-post.php">
    </form>
    <script>
        document.getElementById("redirect").submit();
    </script>';
}
?>

<head>
    <title>Pans@Home - Lets get cooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all_pages.css">
    <link rel="stylesheet" href="css/new_post.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <!-- Check if the user is banned -->
    <script type="text/javascript">
        // Check if the user is banned
        $.ajax({
            url: 'isBanned.php?username=<?= $_SESSION['username'] ?>',
            type: 'GET',
            success: function (data) {
                if (data == 'true') {
                    alert('You are banned from posting');
                    window.location.href = 'home.php';
                }
            }
        });
    </script>
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
    <div class="content-div">
        <div id="page-align">
            <div id="page-title">
                <h2>Create a post</h2>
            </div>
            <div id="new_post">
                <div id="post-type">
                    <script>
                        function typeSelect(button) {
                            if (button.id == "text-post") {

                                $("#postType").val("text");
                                const sibling = button.nextElementSibling;
                                button.disabled = true;
                                button.classList.add("selected");
                                sibling.disabled = false;
                                sibling.classList.remove("selected");
                                var show = document.querySelector("#text-content");
                                var hide = document.querySelector("#img-content");
                                hide.querySelector("input").required = false;
                            } else {
                                $("#postType").val("image");
                                const sibling = button.previousElementSibling;
                                button.disabled = true;
                                button.classList.add("selected");
                                sibling.disabled = false;
                                sibling.classList.remove("selected");

                                var show = document.querySelector("#img-content");
                                var hide = document.querySelector("#text-content");
                                show.querySelector("input").required = true;
                            }

                            show.classList.remove("hide");
                            hide.classList.add("hide");
                        }

                    </script>
                    <button disabled="true" id="text-post" class="selected" onclick="typeSelect(this)">
                        <img src="resources/text.svg">
                        <h3>Text</h3>
                    </button>
                    <button id="img-post" onclick="typeSelect(this)">
                        <img src="resources/picture.svg">
                        <h3>Image</h3>
                    </button>
                </div>
                <form id="form-thing" method="POST" action="submitPost.php" enctype="multipart/form-data">
                    <p id="title-error" class='error'></p>
                    <textarea id="post-title" name="title" placeholder="Title" oninput="autoResize(this)"
                        required></textarea>
                    <textarea id="text-content" name="text" placeholder="Text (Optional)"
                        oninput="autoResize(this)"></textarea>
                    <div id="img-content" class="hide">
                        <label>Upload an image:</label>
                        <input type="file" name="image" id='postImage'
                            accept="image/png,image/gif,image/jpeg,image/webp">
                    </div>
                    <input type='hidden' id='postType' name='postType' value='text'>
                    <button type="submit" id="submitPost">Post</button>
                </form>
                <script>
                    function autoResize(textarea) {
                        textarea.style.height = "1.2em";
                        textarea.style.height = (textarea.scrollHeight + 5) + "px";
                    }
                </script>
            </div>
        </div>
    </div>
    <script src='js/submitPost.js'></script>
</body>

</html>