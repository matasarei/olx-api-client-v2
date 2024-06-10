<?php

use Gentor\Olx\Api\Client;
use Gentor\Olx\Api\Threads;
use PHPUnit\Framework\TestCase;

class ThreadsTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->client->method('threads')->willReturn(new Threads($this->client));
    }

    public function testList()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/threads', ['advert_id' => 1, 'interlocutor_id' => 2, 'limit' => 10, 'offset' => 0])
            ->willReturn([['id' => 1, 'name' => 'Thread 1'], ['id' => 2, 'name' => 'Thread 2']]);

        $response = $this->client->threads()->list(1, 2, 10, 0);

        $this->assertEquals([['id' => 1, 'name' => 'Thread 1'], ['id' => 2, 'name' => 'Thread 2']], $response);
    }

    public function testGetMessages()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/threads/1/messages', ['limit' => 10, 'offset' => 0])
            ->willReturn([['id' => 1, 'text' => 'Hello'], ['id' => 2, 'text' => 'Hi']]);

        $response = $this->client->threads()->getMessages(1, 10, 0);

        $this->assertEquals([['id' => 1, 'text' => 'Hello'], ['id' => 2, 'text' => 'Hi']], $response);
    }

    public function testGetMessage()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/threads/1/messages/2')
            ->willReturn(['id' => 2, 'text' => 'Hi']);

        $response = $this->client->threads()->getMessage(1, 2);

        $this->assertEquals(['id' => 2, 'text' => 'Hi'], $response);
    }

    public function testReply()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/threads/1/messages', ['text' => 'Hello', 'attachments' => ['url1', 'url2']])
            ->willReturn(['id' => 3, 'text' => 'Hello']);

        $response = $this->client->threads()->reply(1, 'Hello', ['url1', 'url2']);

        $this->assertEquals(['id' => 3, 'text' => 'Hello'], $response);
    }

    public function testMarkAsRead()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/threads/1/commands', ['command' => 'mark-as-read'])
            ->willReturn(['status' => 'success']);

        $response = $this->client->threads()->markAsRead(1);

        $this->assertEquals(['status' => 'success'], $response);
    }

    public function testSetFavorite()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/threads/1/commands', ['command' => 'set-favourite', 'is_favourite' => true])
            ->willReturn(['status' => 'success']);

        $response = $this->client->threads()->setFavorite(1, true);

        $this->assertEquals(['status' => 'success'], $response);
    }
}