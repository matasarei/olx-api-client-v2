<?php

namespace Gentor\Olx\Api;

/**
 * Class User
 *
 * @package Gentor\Olx\Api
 */
class User extends ApiResource
{
    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return 'partner/users';
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
     * @return array
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccountBalance()
    {
        return $this->request('GET', $this->getEndpoint() . '/me/account-balance');
    }

    /**
     * @return array
     *
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPaymentMethods()
    {
        return $this->request('GET', $this->getEndpoint() . '/me/payment-methods');
    }
}
