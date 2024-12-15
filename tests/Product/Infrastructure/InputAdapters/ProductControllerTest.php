<?php

namespace App\Tests\Product\Infrastructure\InputAdapters;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testCreateProduct(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/product_create', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'uuid_client' => 'c014a415-4113-49e5-80cb-cc3158c15236',
            'name' => 'Nuevo producto',
            'description' => 'Descripción prueba',
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('product', $responseData);
        $this->assertEquals('Nuevo producto', $responseData['product']['name']);
    }

    public function testDeleteProduct(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/product_delete', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'uuid_client' => 'c014a415-4113-49e5-80cb-cc3158c15236',
            'uuid_product' => '9a6ae1c0-3bc6-41c8-975a-4de5b4357666',
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Product deleted successfully', $responseData['message']);
    }
}
