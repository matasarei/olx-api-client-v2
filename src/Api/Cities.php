<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Exception\GuzzleException;

class Cities extends ApiResource
{
    public function getEndpoint(): string
    {
        return 'partner/cities';
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function list($limit = 0, $offset = 0): array
    {
        if ($limit > 0) {
            return $this->getWithLimit($limit, $offset);
        }

        return $this->getAll();
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getCityDistricts(int $cityId): array
    {
        $response = $this->client->request('GET', sprintf('%s/%d/districts', $this->getEndpoint(), $cityId));

        return $response['data'];
    }
}
