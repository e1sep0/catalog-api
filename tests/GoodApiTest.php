<?php

namespace App\Tests;

use App\Entity\Category;
use App\Entity\Good;

class GoodApiTest extends AbstractTest
{

    public function testCreateGood(): void
    {
        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/categories',
            [
                'json' => [
                    'name' => 'Новая категория',
                ],
            ]
        );

        $categoryIri = $this->findIriBy(Category::class, ['name' => 'Новая категория']);

        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/goods',
            [
                'json' => [
                    'name' => 'Новый товар',
                    'categories' => [
                        $categoryIri,
                    ],
                    'price' => 300,
                ],
            ]
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(
            [
                '@context' => '/api/contexts/Good',
                '@type' => 'Good',
                'name' => 'Новый товар',
                'price' => 300,
            ]
        );
        self::assertMatchesRegularExpression('~^/api/goods/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Good::class);
    }

    public function testCreateInvalidGood(): void
    {
        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/goods',
            [
                'json' => [
                    'name' => '',
                    'price' => 0,
                ],
            ]
        );

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains(
            [
                '@context' => '/api/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'hydra:description' => "name: This value should not be blank.\nprice: This value should be greater than 0.",
            ]
        );
    }

    public function testGetCollectionFromCategory(): void
    {
        $category = static::getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy(
            ['name' => 'Новая категория']
        );

        $response = static::createClient()->request('GET', '/api/categories/'.$category->getId().'/goods');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(
            [
                '@context' => '/api/contexts/Good',
                '@type' => 'hydra:Collection',
            ]
        );

        $this->assertCount(1, $response->toArray()['hydra:member']);
        self::assertMatchesResourceCollectionJsonSchema(Good::class);

        self::assertMatchesRegularExpression('~^/api/categories/\d+/goods$~', $response->toArray()['@id']);
        self::assertMatchesRegularExpression('~^/api/goods/\d+~', $response->toArray()['hydra:member'][0]['@id']);
    }


    public function testUpdateGood(): void
    {
        $client = $this->createClientWithCredentials();

        $iri = $this->findIriBy(Good::class, ['name' => 'Новый товар']);

        $client->request(
            'PUT',
            $iri,
            [
                'json' => [
                    'price' => 500,
                ],
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(
            [
                '@context' => '/api/contexts/Good',
                '@id' => $iri,
                '@type' => 'Good',
                'name' => 'Новый товар',
                'price' => 500,
            ]
        );
    }

    public function testDeleteGood(): void
    {
        $client = $this->createClientWithCredentials();
        $iri = $this->findIriBy(Good::class, ['name' => 'Новый товар']);

        $client->request('DELETE', $iri);

        self::assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Good::class)->findOneBy(
                ['name' => 'Новый товар']
            )
        );

        $categoryIri = $this->findIriBy(Category::class, ['name' => 'Новая категория']);
        $client->request('DELETE', $categoryIri);

    }

}
