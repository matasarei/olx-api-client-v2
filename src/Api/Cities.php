<?php

namespace Gentor\Olx\Api;

/**
 * Class Cities
 *
 * @package Gentor\Olx\Api
 */
class Cities extends ApiResource
{
    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return 'partner/cities';
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array|null
     *
     * @throws OlxException
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list($limit = 0, $offset = 0)
    {
        if ($limit > 0) {
            return $this->getWithLimit($limit, $offset);
        }

        return $this->getAll();
    }

    /**
     * @param int $cityId
     *
     * @return array
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCityDistricts(int $cityId)
    {
        $response = $this->client->request('GET', sprintf('%s/%d/districts', $this->getEndpoint(), $cityId));

        return $response['data'];
    }
}
