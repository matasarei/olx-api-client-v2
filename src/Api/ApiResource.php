<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Exception\GuzzleException;

abstract class ApiResource
{
    protected Client $client;

    abstract public function getEndpoint(): string;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function get(int $id): array
    {
        return $this->request('GET', sprintf('%s/%d', $this->getEndpoint(), $id));
    }

    /**
     * @return array|null
     *
     * @throws OlxException
     * @throws GuzzleException
     */
    public function list()
    {
        return $this->getAll();
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    protected function request(string $method = 'GET', string $endpoint = null, array $data = []): array
    {
        return $this->client->request($method, $endpoint ?? $this->getEndpoint(), $data);
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array|null
     *
     * @throws OlxException
     * @throws GuzzleException
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
     * @throws GuzzleException
     */
    protected function getAll()
    {
        return $this->request('GET', $this->getEndpoint());
    }
}
