<?php

namespace App\Tests\ApiV2Tests;

use App\Entity\Category;
use App\Tests\AbstractTest;

class CategoryApiTest extends AbstractTest
{
    public function testCreateCategory(): void
    {
        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/v2/categories',
            [
                'json' => [
                    'name' => 'Новая категория',
                ],
            ]
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains(
            [
                'name' => 'Новая категория',
            ]
        );
        self::assertMatchesRegularExpression('~^\d+$~', $response->toArray()['id']);
    }

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/v2/categories');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($response->getContent(true), true);
        $this->assertArrayHasKey('name', $data[0]);
    }


    public function testCreateInvalidCategory(): void
    {
        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/v2/categories',
            [
                'json' => [
                    'name' => '',
                ],
            ]
        );

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($response->getContent(false), true);
        $this->assertArrayHasKey('field', $data[0]);
        $this->assertArrayHasKey('message', $data[0]);

        $this->assertContains('This value should not be blank.', $data[0]);
    }

    public function testUpdateCategory(): void
    {
        $client = $this->createClientWithCredentials();

        $category = static::getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy(
            ['name' => 'Новая категория']
        );

        $client->request(
            'POST',
            '/api/v2/categories/' . $category->getId(),
            [
                'json' => [
                    'name' => 'Измененная новая категория',
                ],
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(
            [
                'id' => $category->getId(),
                'name' => 'Измененная новая категория',
            ]
        );
    }

    public function testDeleteCategory(): void
    {
        $client = $this->createClientWithCredentials();
        $category = static::getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy(
            ['name' => 'Измененная новая категория']
        );

        $response = $client->request('DELETE', '/api/v2/categories/' . $category->getId());

        self::assertResponseStatusCodeSame(200);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy(
                ['name' => 'Измененная новая категория']
            )
        );
    }

}
