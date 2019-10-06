<?php

namespace Gentor\Olx\Api;

/**
 * Class Categories
 *
 * @package Gentor\Olx\Api
 */
class Categories extends ApiResource
{
    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return 'partner/categories';
    }

    /**
     * @param int $categoryId
     *
     * @return array
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAttributes(int $categoryId)
    {
        $response = $this->client->request('GET', sprintf('%s/%d/attributes', $this->getEndpoint(), $categoryId));

        return $response['data'];
    }
}
