<?php
require_once("../constants.php");

// Check if all the fields are set and not empty
if (
    isset($_POST['username']) && strlen($_POST['username']) > 0 &&
    isset($_POST['password']) && strlen($_POST['password']) > 0 &&
    isset($_POST['passwordconfirm']) && strlen($_POST['passwordconfirm']) > 0 &&
    isset($_POST['email']) && strlen($_POST['email']) > 0
) {
    // Validate data
    if (strlen($_POST['username']) < 3) {
        echo "Username must be at least 3 characters long";
        exit;
    }
    if (strlen($_POST['username']) > 30) {
        echo "Username must be at most 30 characters long";
        exit;
    }
    
    if (strlen($_POST['password']) < 8) {
        echo "Password must be at least 8 characters long";
        exit;
    }
    if (strlen($_POST['password']) > 30) {
        echo "Password must be at most 30 characters long";
        exit;
    }
    if ($_POST['password'] != $_POST['passwordconfirm']) {
        echo "Passwords do not match";
        exit;
    }
    // Password must have at least one uppercase letter, one lowercase letter, one number, and one special character
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $_POST['password'])) {
        echo "Password must have at least: <ul style='text-align: left'><li>one uppercase letter</li><li>one lowercase letter</li><li>one number</li><li>one special character</li></ul>";
        exit;
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email";
        exit;
    }

    try {
        // Connect to the database
        $conString = 'mysql:host='.DBHOST.';dbname='.DBNAME;
        $pdo = new PDO($conString, DBUSER, DBPASS);

        // Check if the username is already taken or the email is already in use
        $sql = 'select username, email from user where username = :username or email = :email';
        $statement = $pdo->prepare($sql);

        $statement->bindValue(':username', $_POST['username']);
        $statement->bindValue(':email', $_POST['email']);

        $result = $statement->execute();

        if ($result) {
            $row = $statement->fetch();

            if ($row && $row['username'] == $_POST['username']) {
                // Create form to redirect to login.php with error message in POST
                echo 'Username already taken';
                exit;
            } else if ($row && $row['email'] == $_POST['email']) {
                // Create form to redirect to login.php with error message in POST
                echo 'You already have an account with this email';
                exit;
            }
        }

        $sql = 'insert into user (username, email, password, pfp, joinDate, points, admin, banned) values (:username, :email, :password, :pfp, :joinDate, 0, false, false)';
        $statement = $pdo->prepare($sql);

        $options = [
            'cost' => 9,
        ];
        $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);

        $fileContent = file_get_contents('resources/default_user.png');

        $statement->bindValue(':username',$_POST['username']);
        $statement->bindValue(':email',$_POST['email']);
        $statement->bindValue(':password',$hashed_password);
        $statement->bindValue(':pfp',$fileContent,PDO::PARAM_LOB);
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('America/Vancouver'));
        $statement->bindValue(':joinDate',$now->format('Y-m-d H:i:s'));

        $statement->execute();

        $pdo = null;

        // Save the username and pfp in the session
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $_POST['username'];

        echo 'success';
        exit;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    echo 'All fields are required';
    exit;
}
?>