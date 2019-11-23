<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Class Client
 * @package Gentor\Olx\Api
 */
class Client
{
    const OLX_PL = 'pl';
    const OLX_BG = 'bg';
    const OLX_RO = 'ro';
    const OLX_PT = 'pt';
    const OLX_UA = 'ua';
    const OLX_KZ = 'kz';
    const OLX_UZ = 'uz';

    /**
     * @var array
     */
    protected $hosts = [
        self::OLX_PL => "https://www.olx.pl",
        self::OLX_BG => "https://www.olx.bg",
        self::OLX_RO => "https://www.olx.ro",
        self::OLX_PT => "https://www.olx.pt",
        self::OLX_UA => "https://www.olx.ua",
        self::OLX_KZ => "https://www.olx.kz",
        self::OLX_UZ => "https://www.olx.uz"
    ];

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var string|null
     */
    protected $token;

    /**
     * @var string|null
     */
    protected $refreshToken;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var Cities $cities
     */
    protected $cities;

    /**
     * @var Categories $categories
     */
    protected $categories;

    /**
     * @var Adverts $adverts
     */
    protected $adverts;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param Credentials $credentials
     * @param string $countryCode
     *
     * @throws OlxException
     */
    public function __construct(Credentials $credentials, string $countryCode)
    {
        $this->credentials = $credentials;

        if (!array_key_exists($countryCode, $this->hosts)) {
            throw new OlxException(sprintf('Country "%s" is not supported!', $countryCode));
        }

        $this->country = $countryCode;

        $this->client = new GuzzleClient([
            'base_uri' => $this->hosts[$this->country] . '/api/',
            'headers' => $this->getHeaders(false)
        ]);

        $this->cities = new Cities($this);
        $this->categories = new Categories($this);
        $this->adverts = new Adverts($this);
        $this->user = new User($this);
    }

    /**
     * @param string $redirectUrl Url witch handles income requests from the OLX API
     * @param string $state Random hash that identifies the request
     *
     * @return string
     */
    public function getConnectUrl(string $redirectUrl, string $state)
    {
        return sprintf(
            '%s/oauth/authorize/?%s',
            $this->hosts[$this->country],
            http_build_query([
                'client_id' => $this->credentials->getClientId(),
                'response_type' => 'code',
                'state' => $state,
                'scope' => 'read write v2',
                'redirect_uri' => $redirectUrl
            ])
        );
    }

    /**
     * @return Cities
     */
    public function cities()
    {
        return $this->cities;
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @return Adverts
     */
    public function adverts()
    {
        return $this->adverts;
    }

    /**
     * @return Categories
     */
    public function categories()
    {
        return $this->categories;
    }

    /**
     * @param string|null $accessCode
     * @param string|null $redirectUrl
     *
     * @return mixed|string|null
     *
     * @throws OlxException
     */
    public function generateToken(string $accessCode = null, string $redirectUrl = null)
    {
        try {
            if (null !== $accessCode) {
                if (empty($redirectUrl)) {
                    throw new OlxException('Redirect URL must be provided when using access code to generate access token');
                }

                $response = $this->generateAccountToken($accessCode, $redirectUrl);
            } elseif (null !== $this->refreshToken) {
                $response = $this->refreshAccountToken($this->refreshToken);
            } else {
                $response = $this->generateClientToken();
            }
        } catch (ClientException $e) {
            $this->handleException($e);
        }

        $token = $this->handleResponse($response);
        $this->token = $token['access_token'];
        $this->refreshToken = $token['refresh_token'] ?? null;

        return $token['access_token'];
    }

    /**
     * @param string $accessCode
     * @param string $redirectUrl
     *
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    protected function generateAccountToken(string $accessCode, string $redirectUrl)
    {
        return $this->client->post('open/oauth/token',
            [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->credentials->getClientId(),
                    'client_secret' => $this->credentials->getClientSecret(),
                    'scope' => 'v2 read write',
                    'code' => $accessCode,
                    'redirect_uri' => $redirectUrl
                ]
            ]
        );
    }

    /**
     * @param string $refreshToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function refreshAccountToken(string $refreshToken)
    {
        return $this->client->post('open/oauth/token',
            [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->credentials->getClientId(),
                    'client_secret' => $this->credentials->getClientSecret(),
                    'refresh_token' => $refreshToken
                ]
            ]
        );
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function generateClientToken()
    {
        return $this->client->post('open/oauth/token',
            [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->credentials->getClientId(),
                    'client_secret' => $this->credentials->getClientSecret(),
                    'scope' => 'v2 read write',
                ]
            ]
        );
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param bool $includeToken
     *
     * @return array
     *
     * @throws OlxException
     */
    protected function getHeaders($includeToken = true)
    {
        $headers = [
            'Accept' => 'application/json',
            'Version' => '2.0'
        ];

        if ($includeToken) {
            if (empty($this->token)) {
                $this->generateToken();
            }
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        return $headers;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     *
     * @return array
     *
     * @throws OlxException
     * @throws GuzzleException
     */
    public function request(string $method, string $endpoint, $data = [])
    {
        switch ($method) {
            case 'GET':
                $options = [
                    'query' => $data,
                    'headers' => $this->getHeaders()
                ];
                break;
            case 'DELETE':
                $options = [
                    'headers' => $this->getHeaders()
                ];
                break;
            default:
                $options = [
                    'json' => $data,
                    'headers' => $this->getHeaders()
                ];
        }

        try {
            $response = $this->client->request($method, $endpoint, $options);
        } catch (ClientException $e) {
            $this->handleException($e);

            return [];
        }

        return $this->handleResponse($response);
    }

    /**
     * @param Response $response
     *
     * @return mixed
     */
    private function handleResponse(Response $response)
    {
        $stream = stream_for($response->getBody());
        $data = json_decode($stream, true, 512, JSON_UNESCAPED_UNICODE);

        return $data;
    }

    /**
     * @param ClientException $e
     *
     * @throws OlxException
     */
    private function handleException(ClientException $e)
    {
        $stream = stream_for($e->getResponse()->getBody());
        $details = json_decode($stream, true, 512, JSON_UNESCAPED_UNICODE);
        $message = null;

        if (!empty($details['error']['message'])) {
            $message = $details['error']['message'];
        } elseif (!empty($details['error_description'])) {
            $message = $details['error_description'];
        }

        throw new OlxException($message ?? $e->getMessage(), $e->getCode(), $details);
    }
}