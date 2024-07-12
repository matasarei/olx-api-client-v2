<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Exception\GuzzleException;

class Categories extends ApiResource
{
    public function getEndpoint(): string
    {
        return 'partner/categories';
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getAttributes(int $categoryId): array
    {
        $response = $this->client->request(
            'GET',
            sprintf('%s/%d/attributes', $this->getEndpoint(), $categoryId)
        );

        return $response['data'];
    }
}
