<?php

namespace App\Tests\ApiV2Tests;

use App\Entity\Category;
use App\Entity\Good;
use App\Tests\AbstractTest;

class GoodApiTest extends AbstractTest
{

    public function testCreateGood(): void
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

        $categoryId = $response->toArray()['id'];

        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/v2/goods',
            [
                'json' => [
                    'name' => 'Новый товар',
                    'categories' => [
                        $categoryId,
                    ],
                    'price' => 300,
                ],
            ]
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains(
            [
                'name' => 'Новый товар',
                'price' => 300,
            ]
        );

        $categories = $response->toArray()['categories'];

        $this->assertArrayHasKey('name', $categories[0]);
        $this->assertContains('Новая категория', $categories[0]);
    }

    public function testCreateInvalidGood(): void
    {
        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/v2/goods',
            [
                'json' => [
                    'name' => '',
                    'price' => 0,
                ],
            ]
        );

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($response->getContent(false), true);

        foreach ($data as $error) {
            $this->assertArrayHasKey('field', $error);
            $this->assertArrayHasKey('message', $error);
        }

        $this->assertContains('This value should not be blank.', $data[0]);
        $this->assertContains('This value should be greater than 0.', $data[1]);
    }

    public function testUpdateGood(): void
    {
        $client = $this->createClientWithCredentials();

        $good = static::getContainer()->get('doctrine')->getRepository(Good::class)->findOneBy(
            ['name' => 'Новый товар']
        );

        $client->request(
            'POST',
            '/api/v2/goods/' . $good->getId(),
            [
                'json' => [
                    'name' => 'Измененный товар',
                    'price' => 500,
                ],
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(
            [
                'name' => 'Измененный товар',
                'price' => 500,
            ]
        );
    }

    public function testDeleteGood(): void
    {
        $client = $this->createClientWithCredentials();
        $good = static::getContainer()->get('doctrine')->getRepository(Good::class)->findOneBy(
            ['name' => 'Измененный товар']
        );

        $client->request('DELETE', '/api/v2/goods/' . $good->getId());

        self::assertResponseStatusCodeSame(200);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Good::class)->findOneBy(
                ['name' => 'Измененный товар']
            )
        );

        $category = static::getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy(
            ['name' => 'Новая категория']
        );

        $client->request('DELETE', '/api/v2/categories/' . $category->getId());
    }

}
