<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class statFetcherTest extends TestCase {
    public function testInvalidRequest() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'statfetcher.php', [
            'form_params' => [
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        // Make sure the response is a JSON object
        $this->assertJson($response->getBody());
        // Make sure the response is an object with a success property
        $this->assertNotNull(json_decode($response->getBody())->success);
        // Make sure that the success property is false
        $this->assertEquals(0, json_decode($response->getBody())->success);
        // Make sure the response is an object with a response property
        $this->assertNotNull(json_decode($response->getBody())->response);
        // Make sure that the response property is the right error message
        $this->assertEquals("Not a valid query", json_decode($response->getBody())->response);
    }

    public function testInvalidQuery() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'statfetcher.php', [
            'form_params' => [
                'query' => 69,
                'modifier' => 7,
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        // Make sure the response is a JSON object
        $this->assertJson($response->getBody());
        // Make sure the response is an object with a success property
        $this->assertNotNull(json_decode($response->getBody())->success);
        // Make sure that the success property is false
        $this->assertEquals(0, json_decode($response->getBody())->success);
        // Make sure the response is an object with a response property
        $this->assertNotNull(json_decode($response->getBody())->response);
        // Make sure that the response property is the right error message
        $this->assertEquals("Not a valid query", json_decode($response->getBody())->response);
    }

    public function testMissingModifier() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'statfetcher.php', [
            'form_params' => [
                'query' => 4,
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        // Make sure the response is a JSON object
        $this->assertJson($response->getBody());
        // Make sure the response is an object with a success property
        $this->assertNotNull(json_decode($response->getBody())->success);
        // Make sure that the success property is true
        $this->assertEquals(1, json_decode($response->getBody())->success);
        // Make sure the response is an object with a response property
        $this->assertNotNull(json_decode($response->getBody())->response);
        // Make sure that the response property is an array from the database
        $this->assertIsArray(json_decode($response->getBody())->response);
    }

    public function testValidRequest() {
        // Set up the testing environment
        $client = new Client([
            'base_uri' => 'http://localhost/project/',
            'http_errors' => false,
        ]);

        // Make the HTTP request and get the response
        $response = $client->request('POST', 'statfetcher.php', [
            'form_params' => [
                'query' => 1,
                'modifier' => 7,
            ]
        ]);

        // Test the response
        $this->assertEquals(200, $response->getStatusCode());
        // Make sure the response is a JSON object
        $this->assertJson($response->getBody());
        // Make sure the response is an object with a success property
        $this->assertNotNull(json_decode($response->getBody())->success);
        // Make sure that the success property is true
        $this->assertEquals(1, json_decode($response->getBody())->success);
        // Make sure the response is an object with a response property
        $this->assertNotNull(json_decode($response->getBody())->response);
        // Make sure that the response property is an array from the database
        $this->assertIsArray(json_decode($response->getBody())->response);
    }

}


?>