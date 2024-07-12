<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Exception\GuzzleException;

class User extends ApiResource
{
    public function getEndpoint(): string
    {
        return 'partner/users';
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
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getAccountBalance(): array
    {
        return $this->request('GET', $this->getEndpoint() . '/me/account-balance');
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getPaymentMethods(): array
    {
        return $this->request('GET', $this->getEndpoint() . '/me/payment-methods');
    }
}
