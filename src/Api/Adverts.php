<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Exception\GuzzleException;

class Adverts extends ApiResource
{
    public function getEndpoint(): string
    {
        return 'partner/adverts';
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function list(int $limit = 0, int $offset = 0): array
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
    public function create(array $advert): array
    {
        return $this->request('POST', $this->getEndpoint(), $advert);
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function update(int $id, array $advert): array
    {
        return $this->request('PUT', sprintf('%s/%d', $this->getEndpoint(), $id), $advert);
    }

	/**
	 * @throws OlxException
	 * @throws GuzzleException
	 */
    public function delete(int $id): array
    {
        return $this->client->request('DELETE', sprintf('%s/%d', $this->getEndpoint(), $id));
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function activate(int $id): array
    {
        return $this->client->request('POST', sprintf('%s/%d/commands', $this->getEndpoint(), $id), [
            'command' => 'activate',
        ]);
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function deactivate(int $id, bool $isSuccess): array
    {
        return $this->client->request('POST', sprintf('%s/%d/commands', $this->getEndpoint(), $id), [
            'command' => 'deactivate',
            'is_success' => $isSuccess,
        ]);
    }
}
