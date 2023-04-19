<?php 

require_once("../constants.php");
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo '<form id="redirect" method="post" action="login.php">
        <input type="hidden" name="redirect" value="new-post.php">
    </form>
    <script>
        document.getElementById("redirect").submit();
    </script>';
}

$userName = $_SESSION["username"];
if(isset($_POST['postType']) && $_POST['postType'] =='text'){
    try{
        $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('America/Vancouver'));

        $sql = "INSERT INTO post (title, text, upvotes, downvotes, numComments, username,postDate) VALUES (:title, :text, 0, 0,0, :username, :postDate)";
        $statement = $pdo->prepare($sql);

        $statement->bindValue(":title",$_POST["title"]);

        if(isset($_POST['text']) && strlen($_POST["text"]) > 0)
        {
            $statement->bindValue(":text",$_POST["text"]);
        }else{
            $statement->bindValue(":text","");
        }

        $statement->bindValue(':username',$userName);
        $statement->bindValue(":postDate",$now->format('Y-m-d H:i:s'));

        $result = $statement->execute();

        if ($result) {
            echo 'success';
        } else {
            echo 'failure';
        }
    
        $last_id = $pdo->lastInsertId();
        header("Location:post.php?id=".$last_id);
        $pdo = null;
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
else
{
    try{

        $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('America/Vancouver'));
    
        $sql = "INSERT INTO post (title, postImage , upvotes, downvotes, numComments, username,postDate) VALUES (:title, :postImage, 0, 0,0, :username, :postDate)";
        $statement = $pdo->prepare($sql);
    
        $statement->bindValue(":title",$_POST["title"]);

        $image = file_get_contents($_FILES["image"]["tmp_name"]);
        $statement->bindValue(":postImage",$image);
    

        $statement->bindValue(':username',$userName);
        $statement->bindValue(":postDate",$now->format('Y-m-d H:i:s'));

        $result = $statement->execute();

        $last_id = $pdo->lastInsertId();
        header("Location:post.php?id=".$last_id);
        $pdo = null;
        exit;
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>
