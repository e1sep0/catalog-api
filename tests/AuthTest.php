<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

final class AuthTest extends ApiTestCase
{

    public function testSuccessLoginAsUser()
    {
        $response = static::createClient()->request(
            'POST',
            '/authentication_token',
            [
                'headers' => ['Content-Type' => 'application/json', 'accept' => 'application/json'],
                'json' => [
                    'email' => 'admin@admin.com',
                    'password' => 'password',
                ],
            ]
        );

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }

    public function testFailLoginAsUser()
    {
        $response = static::createClient()->request(
            'POST',
            '/authentication_token',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'email' => 'admin@admin.com',
                    'password' => 'wrong_pass',
                ],
            ]
        );

        self::assertJsonContains(['message' => 'Invalid credentials.']);
        self::assertResponseStatusCodeSame('401');
    }
}
