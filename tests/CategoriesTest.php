<?php

use Gentor\Olx\Api\Categories;
use Gentor\Olx\Api\Client;
use PHPUnit\Framework\TestCase;

class CategoriesTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->client->method('categories')->willReturn(new Categories($this->client));
    }

    public function testGet()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/categories/1')
            ->willReturn(['id' => 1, 'name' => 'Category 1']);

        $response = $this->client->categories()->get(1);

        $this->assertEquals(['id' => 1, 'name' => 'Category 1'], $response);
    }

    public function testList()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/categories')
            ->willReturn([['id' => 1, 'name' => 'Category 1'], ['id' => 2, 'name' => 'Category 2']]);

        $response = $this->client->categories()->list();

        $this->assertEquals([['id' => 1, 'name' => 'Category 1'], ['id' => 2, 'name' => 'Category 2']], $response);
    }
}
