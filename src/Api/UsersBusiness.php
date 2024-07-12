<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

class UsersBusiness extends ApiResource
{
    public function getEndpoint(): string
    {
        return 'partner/users-business';
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getMe(): array
    {
        return $this->request('GET', $this->getEndpoint() . '/me');
    }

    /**
     * @param array $address Company address, must include next fields: street, number, postcode, city
     *
     * @throws OlxException
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public function put(
        string $name,
        string $description,
        array $address,
        array $phones,
        string $websiteUrl,
        string $subdomain
    ): array {
        return $this->request('PUT', 'partner/users-business/me', [
            "name" => $name,
            "description" => $description,
            "phones" => $phones,
            "address" => $address,
            "subdomain" => $subdomain,
            "website_url" => $websiteUrl
        ]);
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getLogos(): array
    {
        $response = $this->client->request('GET', $this->getEndpoint() . '/me/logos');

        return $response['data'];
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function postLogo(string $logoUrl): array
    {
        return $this->request('POST', $this->getEndpoint() . '/me/logos', [
            "url" => $logoUrl
        ]);
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function deleteLogo(int $id): array
    {
        return $this->request('DELETE', sprintf('%s/me/logos/%d', $this->getEndpoint(), $id));
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getBanners(): array
    {
        $response = $this->client->request('GET', $this->getEndpoint() . '/me/banners');

        return $response['data'];
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function postBanner(string $bannerUrl): array
    {
        return $this->request('POST', $this->getEndpoint() . '/me/banners', [
            "url" => $bannerUrl
        ]);
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function deleteBanner(int $id): array
    {
        return $this->request('DELETE', sprintf('%s/me/banners/%d', $this->getEndpoint(), $id));
    }
}
