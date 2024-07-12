<?php

use Gentor\Olx\Api\Client;
use Gentor\Olx\Api\UsersBusiness;
use PHPUnit\Framework\TestCase;

class UsersBusinessTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->client->method('usersBusiness')->willReturn(new UsersBusiness($this->client));
    }

    public function testGetMe()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users-business/me')
            ->willReturn(['id' => 1, 'name' => 'Business 1']);

        $response = $this->client->usersBusiness()->getMe();

        $this->assertEquals(['id' => 1, 'name' => 'Business 1'], $response);
    }

    public function testPut()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('PUT', 'partner/users-business/me', [
                "name" => "Business 1",
                "description" => "Description",
                "phones" => ["123456789"],
                "address" => ["street" => "Street", "number" => "1", "postcode" => "12345", "city" => "City"],
                "subdomain" => "subdomain",
                "website_url" => "http://website.com"
            ])
            ->willReturn(['status' => 'success']);

        $response = $this->client->usersBusiness()->put(
            "Business 1",
            "Description",
            ["street" => "Street", "number" => "1", "postcode" => "12345", "city" => "City"],
            ["123456789"],
            "http://website.com",
            "subdomain"
        );

        $this->assertEquals(['status' => 'success'], $response);
    }

    public function testGetLogos()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users-business/me/logos')
            ->willReturn(['data' => [['id' => 1, 'url' => 'http://logo.com/logo.png']]]);

        $response = $this->client->usersBusiness()->getLogos();

        $this->assertEquals([['id' => 1, 'url' => 'http://logo.com/logo.png']], $response);
    }

    public function testPostLogo()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/users-business/me/logos', ["url" => "http://logo.com/logo.png"])
            ->willReturn(['status' => 'success']);

        $response = $this->client->usersBusiness()->postLogo("http://logo.com/logo.png");

        $this->assertEquals(['status' => 'success'], $response);
    }

    public function testDeleteLogo()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('DELETE', 'partner/users-business/me/logos/1')
            ->willReturn(['status' => 'success']);

        $response = $this->client->usersBusiness()->deleteLogo(1);

        $this->assertEquals(['status' => 'success'], $response);
    }

    public function testGetBanners()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'partner/users-business/me/banners')
            ->willReturn(['data' => [['id' => 1, 'url' => 'http://banner.com/banner.png']]]);

        $response = $this->client->usersBusiness()->getBanners();

        $this->assertEquals([['id' => 1, 'url' => 'http://banner.com/banner.png']], $response);
    }

    public function testPostBanner()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', 'partner/users-business/me/banners', ["url" => "http://banner.com/banner.png"])
            ->willReturn(['status' => 'success']);

        $response = $this->client->usersBusiness()->postBanner("http://banner.com/banner.png");

        $this->assertEquals(['status' => 'success'], $response);
    }

    public function testDeleteBanner()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with('DELETE', 'partner/users-business/me/banners/1')
            ->willReturn(['status' => 'success']);

        $response = $this->client->usersBusiness()->deleteBanner(1);

        $this->assertEquals(['status' => 'success'], $response);
    }
}
