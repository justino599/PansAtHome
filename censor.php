<?php
if (!isset($_POST['data'])) {
    exit;
}

// This is a simple script to censor words in a string.
require_once("../constants.php");
try {
    $pdo = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);

    $sql = "SELECT * FROM BannedWords";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $censored = array();

    foreach ($result as $row) {
        $censored[] = $row['word'];
    }

    $censored_string = str_replace($censored, "****", $_POST['data']);

    echo $censored_string;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>