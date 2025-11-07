<?php

use Gentor\Olx\Api\Adverts;
use Gentor\Olx\Api\Client;
use PHPUnit\Framework\TestCase;

class AdvertsTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->client->method('adverts')->willReturn(new Adverts($this->client));
    }

    public function testGet()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/adverts/1')
            ->willReturn(['id' => 1, 'title' => 'Advert 1']);

        $response = $this->client->adverts()->get(1);

        $this->assertEquals(['id' => 1, 'title' => 'Advert 1'], $response);
    }

    public function testList()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/adverts')
            ->willReturn([['id' => 1, 'title' => 'Advert 1'], ['id' => 2, 'title' => 'Advert 2']]);

        $response = $this->client->adverts()->list();

        $this->assertEquals([['id' => 1, 'title' => 'Advert 1'], ['id' => 2, 'title' => 'Advert 2']], $response);
    }

    public function testCreate()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/adverts', ['title' => 'Advert 1'])
            ->willReturn(['id' => 1, 'title' => 'Advert 1']);

        $response = $this->client->adverts()->create(['title' => 'Advert 1']);

        $this->assertEquals(['id' => 1, 'title' => 'Advert 1'], $response);
    }

    public function testUpdate()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('PUT', 'partner/adverts/1', ['title' => 'Advert 1'])
            ->willReturn(['id' => 1, 'title' => 'Advert 1']);

        $response = $this->client->adverts()->update(1, ['title' => 'Advert 1']);

        $this->assertEquals(['id' => 1, 'title' => 'Advert 1'], $response);
    }

    public function testDelete()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('DELETE', 'partner/adverts/1')
            ->willReturn([]);

        $response = $this->client->adverts()->delete(1);

        $this->assertEquals([], $response);
    }

    public function testActivate()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/adverts/1/commands', ['command' => 'activate'])
            ->willReturn([]);

        $response = $this->client->adverts()->activate(1);

        $this->assertEquals([], $response);
    }

    public function testDeactivate()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/adverts/1/commands', ['command' => 'deactivate', 'is_success' => true])
            ->willReturn([]);

        $response = $this->client->adverts()->deactivate(1, true);

        $this->assertEquals([], $response);
    }
}
