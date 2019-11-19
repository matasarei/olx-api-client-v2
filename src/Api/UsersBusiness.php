<?php

namespace Gentor\Olx\Api;

/**
 * Class User
 *
 * @package Gentor\Olx\Api
 */
class UsersBusiness extends ApiResource
{
    /**
     * @return string
     */
    public function getEndpoint()
    {
        return 'partner/users-business';
    }

    /**
     * @return array
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMe()
    {
        return $this->request('GET', $this->getEndpoint() . '/me');
    }

    /**
     * @param string $name
     * @param string $description
     * @param array $address Company address, must include next fields: street, number, postcode, city
     * @param array $phones Phone numbers
     * @param string $websiteUrl
     * @param string $subdomain
     *
     * @return array
     *
     * @throws OlxException
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(
        string $name,
        string $description,
        array $address,
        array $phones,
        string $websiteUrl,
        string $subdomain
    ) {
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
     * @return array
     *
     * @throws OlxException
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLogos()
    {
        $response = $this->client->request('GET', $this->getEndpoint() . '/me/logos');

        return $response['data'];
    }

    /**
     * @param string $logoUrl
     *
     * @return array
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postLogo(string $logoUrl)
    {
        return $this->request('PUT', $this->getEndpoint() . '/me/logos', [
            "url" => $logoUrl
        ]);
    }

    /**
     * @param int|null $id
     *
     * @return mixed
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBanners(int $id = null)
    {
        if (null !== $id) {
            return $this->request('GET', sprintf('%s/me/logos/%d', $this->getEndpoint(), $id));
        }

        return $this->request('GET', $this->getEndpoint() . '/me/logos');
    }
}
