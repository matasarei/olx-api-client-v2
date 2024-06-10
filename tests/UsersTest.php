<?php

use Gentor\Olx\Api\Client;
use Gentor\Olx\Api\User;
use PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->client->method('user')->willReturn(new User($this->client));
    }

    public function testGet()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users/1')
            ->willReturn(['id' => 1, 'name' => 'John Doe']);

        $response = $this->client->user()->get(1);

        $this->assertEquals(['id' => 1, 'name' => 'John Doe'], $response);
    }

    public function testList()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users')
            ->willReturn([['id' => 1, 'name' => 'John Doe'], ['id' => 2, 'name' => 'Jane Doe']]);

        $response = $this->client->user()->list();

        $this->assertEquals([['id' => 1, 'name' => 'John Doe'], ['id' => 2, 'name' => 'Jane Doe']], $response);
    }

    public function testGetMe()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users/me')
            ->willReturn(['id' => 1, 'name' => 'John Doe']);

        $response = $this->client->user()->getMe();

        $this->assertEquals(['id' => 1, 'name' => 'John Doe'], $response);
    }

    public function testGetAccountBalance()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users/me/account-balance')
            ->willReturn(['sum' => 123.45]);

        $response = $this->client->user()->getAccountBalance();

        $this->assertEquals(['sum' => 123.45], $response);
    }

    public function testGetPaymentMethods()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users/me/payment-methods')
            ->willReturn(['account', 'postpaid']);

        $response = $this->client->user()->getPaymentMethods();

        $this->assertEquals(['account', 'postpaid'], $response);
    }
}
