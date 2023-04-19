<?php

require_once("../constants.php");
session_start();

if (!isset($_SESSION["username"])) {
    echo 'You are not logged in';
    exit;
}

$userName = $_SESSION["username"];
$commentId = $_POST["commentID"];
$prevVote = '';
try 
{
    #Connection and check if user is in this table
    $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
    $pdo = new PDO($conString, DBUSER, DBPASS);
    $sql = "select upvoted, downvoted from UserVotedComment where commentId = :commentId and username = :userid";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(":commentId", $commentId);
    $statement->bindValue(":userid", $userName);

    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    #If there is a result set VoteType to the according info
    if ($result != null) 
    {
        if ($result['upvoted'] == true) {
            $prevVote = 'upvoted';
        } else if ($result['downvoted'] == true) {
            $prevVote = 'downvoted';
        } else {
            $prevVote = 'no vote';

        }
    } 
    else 
    {
        $prevVote = 'no vote';
        $sql = 'INSERT INTO UserVotedComment (commentId, username, upvoted, downvoted) values (:commentId, :username, 0, 0)';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(":commentId", $commentId);
        $statement->bindValue(":username", $userName);
        $statement->execute();
    }
} 
catch (PDOException $e) 
{
    echo $e->getMessage();
}


if (isset($_POST["vote"]) && $_POST['vote'] == 'upvote') 
{
    try 
    {
        #Connect
        $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);
        #Check what type of vote voteType is, in this case upvoted so remove prev vote 
        if ($prevVote == 'upvoted') 
        {
            $sql = 'UPDATE UserVotedComment set upvoted = 0 where username = :userId and commentId = :commentId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":commentId", $commentId);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);


            #If there was a result then update the other table
            $sql = 'update comment set upvotes = upvotes-1 where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            $update = 'update user join comment using (username) set points = points - 1 where commentId = :commentId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            $sql = 'select upvotes-downvotes as karma from comment where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," .$karma . ",white";
            
        }
        #If downvoted set update to true and downvote to false and relfect changes in the other table
        else if ($prevVote == 'downvoted') 
        {
            $sql = 'UPDATE UserVotedComment set upvoted = 1, downvoted = 0 where username = :userId and commentId = :commentId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":commentId", $commentId);

            $statement->execute();

    
            #Update post to reflect karma change 
            $sql = 'update comment set upvotes = upvotes+1, downvotes = downvotes-1 where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();
            

            #Update user to reflect karma change
            $update = 'update user join comment using (username) set points = points + 2 where commentId = :commentId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from comment where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "orange," . $karma .",white";
            
        } 
        else if ($prevVote == "no vote") 
        {
            #Add update to UserVotedComment for change 
            $sql = 'UPDATE UserVotedComment set upvoted = 1, downvoted = 0 where username = :userId and commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":commentId", $commentId);
            $statement->execute();

            #Update table to reflect karma change 
            $sql = 'update comment set upvotes = upvotes+1 where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'update user join comment using (username) set points = points + 1 where commentId = :commentId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from comment where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "orange," .$karma . ",white";
        }
        $pdo = null;
    } 
    catch (PDOException $e) 
    {
        echo $e->getMessage();
    }
} 


else if (isset($_POST["vote"]) && $_POST['vote'] == 'downvote') 
{
    try 
    {
        #Connect
        $conString = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);
        #Check what type of vote voteType is 
        if ($prevVote == 'upvoted') 
        {
            #Update UserVotedComment to be a downvote
            $sql = 'UPDATE UserVotedComment set upvoted = 0, downvoted = 1 where username = :userId and commentId = :commentId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":commentId", $commentId);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            echo $result;
            
            #remove upvote and increase downvote
            $sql = 'UPDATE comment set upvotes = upvotes-1, downvotes = downvotes+1 where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'UPDATE user join comment using (username) set points = points - 2 where commentId = :commentId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from comment where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," . $karma . ",blue";
            
        }
        #If downvoted set update to true and downvote to false and reflect changes in the other table
        else if ($prevVote == 'downvoted') 
        {
            $sql = 'UPDATE UserVotedComment set downvoted = 0 where username = :userId and commentId = :commentId';
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":userId", $userName);
            $statement->bindValue(":commentId", $commentId);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);


            $sql = 'update comment set downvotes = downvotes-1 where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'update user join comment using (username) set points = points + 1 where commentId = :commentId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from comment where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," . $karma . ",white";
            
        } 
        else if ($prevVote == "no vote") 
        {
            #If no vote then just downvote and set accordingly
            $sql = 'UPDATE UserVotedComment set upvoted = 0, downvoted = 1 where username = :userId and commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $statement ->bindValue(":userId", $userName);
            $result = $statement->execute();

            
            $sql = 'update comment set downvotes = downvotes+1 where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Update user to reflect karma change
            $update = 'update user join comment using (username) set points = points - 1 where commentId = :commentId';
            $statement = $pdo->prepare($update);
            $statement->bindValue(":commentId", $commentId);
            $result = $statement->execute();

            #Get total karma to echo back
            $sql = 'select upvotes-downvotes as karma from comment where commentId = :commentId';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":commentId", $commentId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $karma = $result['karma'];
            echo "white," . $karma . ",blue";
        }
    $pdo = null;
    } 
    catch (PDOException $e) 
    {
        echo $e->getMessage();
    }
}
else 
{
    echo "whoops";
}

?>