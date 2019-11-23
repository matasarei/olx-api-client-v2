<?php

namespace Gentor\Olx\Api;

/**
 * Class Adverts
 *
 * @package Gentor\Olx\Api
 */
class Adverts extends ApiResource
{
    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return 'partner/adverts';
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array|mixed|null
     *
     * @throws OlxException
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
     * @param array $advert
     *
     * @return array
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $advert)
    {
        return $this->request('POST', $this->getEndpoint(), $advert);
    }

    /**
     * @param int $id
     * @param array $advert
     *
     * @return array|null
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(int $id, array $advert)
    {
        return $this->request('PUT', sprintf('%s/%d', $this->getEndpoint(), $id), $advert);
    }

    /**
     * @param int $id
     *
     * @throws OlxException
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(int $id)
    {
        $this->client->request('DELETE', "partner/adverts/{$id}");
    }
}
