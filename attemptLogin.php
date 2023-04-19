<?php
require_once("../constants.php");

if (
    isset($_POST['username']) && strlen($_POST['username']) > 0 &&
    isset($_POST['password']) && strlen($_POST['password']) > 0
) {
    try {
        $conString = 'mysql:host='.DBHOST.';dbname='.DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);

        $sql = 'select password, admin from user where username = :username';
        $statement = $pdo->prepare($sql);
        
        $statement->bindValue(':username', $_POST['username']);
        
        $result = $statement->execute();
        
        if ($result) {
            $row = $statement->fetch();

            if ($row && password_verify($_POST['password'], $row['password'])) {
                session_start();
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['loggedin'] = true;
                $_SESSION['admin'] = $row['admin'];
                echo 'success';
                exit();
            } else {
                echo 'Incorrect Username or Password';
            }
        } else {
            echo 'Incorrect Username or Password';
        }

        $pdo = null;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
} else {
    echo "Incorrect Username or Password";
}

?>