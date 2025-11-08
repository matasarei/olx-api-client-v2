<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class Client
{
    const OLX_PL = 'pl';
    const OLX_BG = 'bg';
    const OLX_RO = 'ro';
    const OLX_PT = 'pt';
    const OLX_UA = 'ua';
    const OLX_KZ = 'kz';

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
    ];

    protected GuzzleClient $client;
    protected ?string $token = null;
    protected ?string $refreshToken = null;
    protected string $country;
    protected Credentials $credentials;
    protected Cities $cities;
    protected Categories $categories;
    protected Adverts $adverts;
    protected Threads $threads;
    protected User $user;
    protected UsersBusiness $usersBusiness;

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
        $this->threads = new Threads($this);
        $this->user = new User($this);
        $this->usersBusiness = new UsersBusiness($this);
    }

    public function cities(): Cities
    {
        return $this->cities;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function usersBusiness(): UsersBusiness
    {
        return $this->usersBusiness;
    }

    public function adverts(): Adverts
    {
        return $this->adverts;
    }

    public function categories(): Categories
    {
        return $this->categories;
    }

    public function threads(): Threads
    {
        return $this->threads;
    }

    /**
     * @param string $redirectUrl Url witch handles income requests from the OLX API
     * @param string $state Random hash that identifies the request
     */
    public function getConnectUrl(string $redirectUrl, string $state): string
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
     * @param string|null $accessCode Access code received from the OLX API (optional)
     * @param string|null $redirectUrl Url witch handles income requests from the OLX API (optional)
     *
     * @throws OlxException
     */
    public function generateToken(string $accessCode = null, string $redirectUrl = null): string
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

    protected function generateAccountToken(string $accessCode, string $redirectUrl): ResponseInterface
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

    protected function refreshAccountToken(string $refreshToken): ResponseInterface
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

    protected function generateClientToken(): ResponseInterface
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

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @throws OlxException
     */
    protected function getHeaders($includeToken = true): array
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
     * @throws OlxException
     * @throws GuzzleException
     */
    public function request(string $method, string $endpoint, array $data = []): array
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
        }

        return $this->handleResponse($response);
    }

    private function handleResponse(Response $response): array
    {
        if ($response->getStatusCode() === 204) {
            return [];
        }

        $body = (string) $response->getBody();
        $data = json_decode($body, true, 512, JSON_UNESCAPED_UNICODE);

        if ($data === null) {
            if (trim($body) === '') {
                throw new OlxException(
                    'API returned empty body for non-204 status code',
                    $response->getStatusCode(),
                    (object) ['status_code' => $response->getStatusCode()]
                );
            }

            throw new OlxException(
                'Failed to decode API response: invalid JSON',
                $response->getStatusCode(),
                (object) [
                    'body' => $body,
                    'json_error' => json_last_error_msg(),
                ]
            );
        }

        return $data;
    }

    /**
     * @throws OlxException
     */
    private function handleException(ClientException $e)
    {
        $body = (string) $e->getResponse()->getBody();
        $details = json_decode($body, false, 512, JSON_UNESCAPED_UNICODE);
        $message = null;

        if (!empty($details->error->message)) {
            $message = $details->error->message;
        } elseif (!empty($details->error_description)) {
            $message = $details->error_description;
        }

        throw new OlxException($message ?? $e->getMessage(), $e->getCode(), $details);
    }
}
