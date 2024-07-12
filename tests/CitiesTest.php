<?php

use Gentor\Olx\Api\Cities;
use Gentor\Olx\Api\Client;
use PHPUnit\Framework\TestCase;

class CitiesTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->client->method('cities')->willReturn(new Cities($this->client));
    }

    public function testGet()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/cities/1')
            ->willReturn(['id' => 1, 'name' => 'City 1']);

        $response = $this->client->cities()->get(1);

        $this->assertEquals(['id' => 1, 'name' => 'City 1'], $response);
    }

    public function testList()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/cities')
            ->willReturn([['id' => 1, 'name' => 'City 1'], ['id' => 2, 'name' => 'City 2']]);

        $response = $this->client->cities()->list();

        $this->assertEquals([['id' => 1, 'name' => 'City 1'], ['id' => 2, 'name' => 'City 2']], $response);
    }

    public function testGetCityDistricts()
    {
        $expectedResponse = [
            ['id' => 1, 'name' => 'District 1'],
            ['id' => 2, 'name' => 'District 2'],
        ];

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/cities/1/districts')
            ->willReturn(['data' => $expectedResponse]);

        $response = $this->client->cities()->getCityDistricts(1);

        $this->assertEquals($expectedResponse, $response);
    }
}
