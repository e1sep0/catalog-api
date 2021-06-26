<?php

namespace App\Tests;

use App\Entity\Category;

class CategoryApiTest extends AbstractTest
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/categories');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(
            [
                '@context' => '/api/contexts/Category',
                '@id' => '/api/categories',
                '@type' => 'hydra:Collection',
            ]
        );
    }

    public function testCreateCategory(): void
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

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(
            [
                '@context' => '/api/contexts/Category',
                '@type' => 'Category',
                'name' => 'Новая категория',
            ]
        );
        self::assertMatchesRegularExpression('~^/api/categories/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Category::class);
    }

    public function testCreateInvalidCategory(): void
    {
        $response = $this->createClientWithCredentials()->request(
            'POST',
            '/api/categories',
            [
                'json' => [
                    'name' => '',
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
                'hydra:description' => 'name: This value should not be blank.',
            ]
        );
    }

    public function testUpdateCategory(): void
    {
        $client = $this->createClientWithCredentials();

        $iri = $this->findIriBy(Category::class, ['name' => 'Новая категория']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Измененная новая категория',
        ]]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@id' => $iri,
            'name' => 'Измененная новая категория',
        ]);
    }

    public function testDeleteCategory(): void
    {
        $client = $this->createClientWithCredentials();
        $iri = $this->findIriBy(Category::class, ['name' => 'Измененная новая категория']);

        $client->request('DELETE', $iri);

        self::assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy(['name' => 'Измененная новая категория'])
        );
    }

}
