<?php

use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'http://localhost/project/',
    'http_errors' => false,
]);

// Make the HTTP request and get the response
$response = $client->request('POST', 'userPfp.php', [
    'form_params' => [
    ]
]);

?>
