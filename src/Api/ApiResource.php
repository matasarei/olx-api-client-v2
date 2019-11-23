<?php

namespace Gentor\Olx\Api;

/**
 * Class ApiResource
 *
 * @package Gentor\Olx\Api
 */
abstract class ApiResource
{
    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @return string
     */
    abstract function getEndpoint();

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(int $id)
    {
        return $this->request('GET', sprintf('%s/%d', $this->getEndpoint(), $id));
    }

    /**
     * @return array|null
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        return $this->getAll();
    }

    /**
     * @param string $method
     * @param string|null $endpoint
     * @param array $data
     *
     * @return array|null
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(string $method = 'GET', string $endpoint = null, array $data = [])
    {
        $response = $this->client->request($method, $endpoint ?? $this->getEndpoint(), $data);

        return $response['data'] ?? null;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array|null
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getWithLimit(int $limit, int $offset = 0)
    {
        return $this->request('GET', $this->getEndpoint(), [
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * @return array|null
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getAll()
    {
        return $this->request('GET', $this->getEndpoint());
    }
}
