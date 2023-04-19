<?php

require_once("../constants.php");
session_start();

if (!isset($_SESSION["username"])) {
    echo 'You are not logged in';
    exit;
}

$userName = $_SESSION["username"];
$postId = $_POST["postID"];
$prevVote = '';
try {
    #Connection and check if user is in this table
    $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    $pdo = new PDO($conString, DBUSER, DBPASS);
    $sql = "select upvoted, downvoted from UserVotedPost where postId = :postId and username = :userid";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(":postId", $postId);
    $statement->bindValue(":userid", $userName);

    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    #If there is a result set VoteType to the according info
    if ($result != null) {
        if ($result['upvoted'] == true) {
            $prevVote = 'upvoted';
        } else if ($result['downvoted'] == true) {
            $prevVote = 'downvoted';
        } else {
            $prevVote = 'no vote';

        }
    } else {
        $prevVote = 'no vote';
        $sql = 'INSERT INTO UserVotedPost (postId, username, upvoted, downvoted) values (:postId, :username, 0, 0)';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(":postId", $postId);
        $statement->bindValue(":username", $userName);
        $statement->execute();
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}


if (isset($_POST["vote"]) && $_POST['vote'] == 'upvote') {
    try {
        #Connect
        $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);
        #Check what type of vote voteType is, in this case upvoted so remove prev vote 
        if ($prevVote == 'upvoted') {
            $sql = 'UPDATE UserVotedPost set upvoted = 0 where username = :userId and postId = :postId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":postId", $postId);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);


            #If there was a result then update the other table
            $sql = 'update post set upvotes = upvotes-1 where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            $update = 'update user join post using (username) set points = points - 1 where postId = :postId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            $sql = 'select upvotes-downvotes as karma from post where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," . $karma . ",white";

        }
        #If downvoted set update to true and downvote to false and relfect changes in the other table
        else if ($prevVote == 'downvoted') {
            $sql = 'UPDATE UserVotedPost set upvoted = 1, downvoted = 0 where username = :userId and postId = :postId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":postId", $postId);

            $statement->execute();


            #Update post to reflect karma change 
            $sql = 'update post set upvotes = upvotes+1, downvotes = downvotes-1 where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();


            #Update user to reflect karma change
            $update = 'update user join post using (username) set points = points + 2 where postId = :postId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from post where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "orange," . $karma . ",white";

        } else if ($prevVote == "no vote") {
            #Add update to UserVotedPost for change 
            $sql = 'UPDATE UserVotedPost set upvoted = 1, downvoted = 0 where username = :userId and postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":postId", $postId);
            $statement->execute();

            #Update table to reflect karma change 
            $sql = 'update post set upvotes = upvotes+1 where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'update user join post using (username) set points = points + 1 where postId = :postId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from post where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "orange," . $karma . ",white";
        }
        $pdo = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else if (isset($_POST["vote"]) && $_POST['vote'] == 'downvote') {
    try {
        #Connect
        $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);
        #Check what type of vote voteType is 
        if ($prevVote == 'upvoted') {
            $sql = 'UPDATE UserVotedPost set upvoted = 0, downvoted = 1 where username = :userId and postId = :postId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":postId", $postId);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            echo $result;


            #If there was a result then update the other table

            #remove upvote and increase downvote
            $sql = 'UPDATE post set upvotes = upvotes-1, downvotes = downvotes+1 where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'UPDATE user join post using (username) set points = points - 2 where postId = :postId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from post where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," . $karma . ",blue";

        }
        #If downvoted set update to true and downvote to false and reflect changes in the other table
        else if ($prevVote == 'downvoted') {
            $sql = 'UPDATE UserVotedPost set downvoted = 0 where username = :userId and postId = :postId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":postId", $postId);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);


            $sql = 'update post set downvotes = downvotes-1 where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'update user join post using (username) set points = points + 1 where postId = :postId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from post where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," . $karma . ",white";

        } else if ($prevVote == "no vote") {

            $sql = 'UPDATE UserVotedPost set upvoted = 0, downvoted = 1 where username = :userId and postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $statement->bindValue(":userId", $userName);
            $result = $statement->execute();


            $sql = 'update post set downvotes = downvotes+1 where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'update user join post using (username) set points = points - 1 where postId = :postId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":postId", $postId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from post where postId = :postId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":postId", $postId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," . $karma . ",blue";
        }
        $pdo = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    echo "whoops";
}

?>