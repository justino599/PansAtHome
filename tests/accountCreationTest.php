<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class accountCreationTest extends TestCase {
    public function testMissingFields() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "Password1!",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("All fields are required", $response->getBody()->getContents());
    }

    public function testShortUsername() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "ha",
                'password' => "Password1!",
                'passwordconfirm' => "Password1!",
                'email' => "test@exmple.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Username must be at least 3 characters long", $response->getBody()->getContents());

    }

    public function testLongUsername() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "A very long username that is way too long",
                'password' => "Password1!",
                'passwordconfirm' => "Password1!",
                'email' => "test@exmple.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Username must be at most 30 characters long", $response->getBody()->getContents());

    }

    public function testShortPassword() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "P",
                'passwordconfirm' => "P",
                'email' => "test@exmple.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Password must be at least 8 characters long", $response->getBody()->getContents());

    }

    public function testLongPassword() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "aifjaijfopewijfoksgoilgsngpjisfgsdgfsdg",
                'passwordconfirm' => "aifjaijfopewijfoksgoilgsngpjisfgsdgfsdg",
                'email' => "test@exmple.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Password must be at most 30 characters long", $response->getBody()->getContents());

    }

    public function testPasswordMismatch() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "Password1!",
                'passwordconfirm' => "Password2!",
                'email' => "test@example.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Passwords do not match", $response->getBody()->getContents());

    }

    public function testInvalidPassword() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "Password1",
                'passwordconfirm' => "Password1",
                'email' => "test@exmple.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString("Password must have at least:", $response->getBody()->getContents());

    }
    
    public function testInvalidEmail() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "Password1!",
                'passwordconfirm' => "Password1!",
                'email' => "testexample.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Invalid email", $response->getBody()->getContents());

    }

    public function testValidCreation() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "Password1!",
                'passwordconfirm' => "Password1!",
                'email' => "test@exmple.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("success", $response->getBody()->getContents());

    }

    public function testDuplicateUsername() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'attemptRegister.php', [
            'form_params' => [
                'username' => "testuser",
                'password' => "Password1!",
                'passwordconfirm' => "Password1!",
                'email' => "test@example.com",
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString("already", $response->getBody()->getContents());

    }

    protected function tearDown(): void{
        // remove testuser from database
        require_once("../constants.php");
        $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);

        $stmt = $pdo->prepare("DELETE FROM user WHERE username = 'testuser'");

        $stmt->execute();

        $pdo = null;
    }
}

?>