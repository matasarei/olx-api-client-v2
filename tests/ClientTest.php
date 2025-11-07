<?php

use Gentor\Olx\Api\Client;
use Gentor\Olx\Api\Credentials;
use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;
use Gentor\Olx\Api\OlxException;
use GuzzleHttp\Psr7\Response;

class ClientTest extends TestCase
{
    private Client $client;
    private HttpClient $httpClient;

    protected function setUp(): void
    {
        $this->client = new Client(
            new Credentials(1234567890, 'client_secret'),
            Client::OLX_UA
        );

        $this->httpClient = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection = new ReflectionProperty(Client::class, 'client');
        $reflection->setValue($this->client, $this->httpClient);
    }

    public function testGetConnectUrl()
    {
        $redirectUrl = 'https://example.com/redirect';
        $state = 'testState';
        $result = $this->client->getConnectUrl($redirectUrl, $state);
        $expectedUrl = sprintf(
            '%s/oauth/authorize/?%s',
            'https://www.olx.ua',
            http_build_query([
                'client_id' => 1234567890,
                'response_type' => 'code',
                'state' => $state,
                'scope' => 'read write v2',
                'redirect_uri' => $redirectUrl
            ])
        );
        $this->assertEquals($expectedUrl, $result);
    }

    public function testGenerateTokenWithAccessAndNoRedirect()
    {
        $this->expectException(OlxException::class);
        $this->client->generateToken('q2w3e4r5t6y7u8i9o0p');
    }

    public function generateTokenDataProvider(): Generator
    {
        $accessToken = 'plmoknijbuhvygctfxrdzeswaq';
        $refreshToken = 'qawsedrftgyhujikolp';

        yield [
            $this->createConfiguredMock(Response::class, [
                'getBody' => '{"access_token": "' . $accessToken . '", "refresh_token": "' . $refreshToken . '"}'
            ]),
            $accessToken,
            $refreshToken,
        ];

        yield [
            $this->createConfiguredMock(Response::class, [
                'getBody' => '{"access_token": "' . $accessToken . '", "refresh_token": null}'
            ]),
            $accessToken,
            null
        ];
    }

    /**
     * @dataProvider generateTokenDataProvider
     */
    public function testGenerateTokenWithAccessAndRedirect(Response $response, string $accessToken, ?string $refreshToken)
    {
        $this->httpClient->method('post')
            ->with('open/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => 1234567890,
                    'client_secret' => 'client_secret',
                    'scope' => 'v2 read write',
                    'code' => 'q2w3e4r5t6y7u8i9o0p',
                    'redirect_uri' => 'https://example.com/redirect'
                ]
            ])->willReturn($response);

        $token = $this->client->generateToken('q2w3e4r5t6y7u8i9o0p', 'https://example.com/redirect');
        $this->assertEquals($accessToken, $token);
        $this->assertEquals($refreshToken, $this->client->getRefreshToken());
    }

    /**
     * @dataProvider generateTokenDataProvider
     */
    public function testGenerateTokenWithRefresh(Response $response, string $accessToken, ?string $refreshToken)
    {
        $this->httpClient->method('post')
            ->with('open/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => 1234567890,
                    'client_secret' => 'client_secret',
                    'refresh_token' => 'refresh_token'
                ]
            ])->willReturn($response);

        $this->client->setRefreshToken('refresh_token');
        $token = $this->client->generateToken();
        $this->assertEquals($accessToken, $token);
        $this->assertEquals($refreshToken, $this->client->getRefreshToken());
    }

    /**
     * @dataProvider generateTokenDataProvider
     */
    public function testGenerateTokenWithClient(Response $response, string $accessToken, ?string $refreshToken)
    {
        $this->httpClient->method('post')
            ->with('open/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 1234567890,
                    'client_secret' => 'client_secret',
                    'scope' => 'v2 read write'
                ]
            ])->willReturn($response);

        $token = $this->client->generateToken();
        $this->assertEquals($accessToken, $token);
        $this->assertEquals($refreshToken, $this->client->getRefreshToken());
    }

    public function testHandleResponse204NoContent()
    {
        // Mock a 204 response with empty body
        $response = $this->createConfiguredMock(Response::class, [
            'getBody' => '',
            'getStatusCode' => 204
        ]);

        $this->httpClient->method('request')
            ->willReturn($response);

        $this->client->setToken('test_token');
        $result = $this->client->request('DELETE', 'partner/adverts/1');

        $this->assertEquals([], $result);
    }

    public function testHandleResponseValidJson()
    {
        // Mock a 200 response with valid JSON
        $response = $this->createConfiguredMock(Response::class, [
            'getBody' => '{"id": 1, "title": "Test"}',
            'getStatusCode' => 200
        ]);

        $this->httpClient->method('request')
            ->willReturn($response);

        $this->client->setToken('test_token');
        $result = $this->client->request('GET', 'partner/adverts/1');

        $this->assertEquals(['id' => 1, 'title' => 'Test'], $result);
    }

    public function testHandleResponseInvalidJsonThrowsException()
    {
        // Mock a 200 response with invalid JSON
        $response = $this->createConfiguredMock(Response::class, [
            'getBody' => 'invalid json',
            'getStatusCode' => 200
        ]);

        $this->httpClient->method('request')
            ->willReturn($response);

        $this->client->setToken('test_token');

        $this->expectException(OlxException::class);
        $this->expectExceptionMessage('Failed to decode API response: invalid JSON');
        $this->client->request('GET', 'partner/adverts/1');
    }

    public function testHandleResponseEmptyBodyNon204ThrowsException()
    {
        // Mock a 200 response with empty body (should be 204)
        $response = $this->createConfiguredMock(Response::class, [
            'getBody' => '',
            'getStatusCode' => 200
        ]);

        $this->httpClient->method('request')
            ->willReturn($response);

        $this->client->setToken('test_token');

        $this->expectException(OlxException::class);
        $this->expectExceptionMessage('API returned empty body for non-204 status code');
        $this->client->request('GET', 'partner/adverts/1');
    }
}
