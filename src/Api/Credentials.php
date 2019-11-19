<?php

namespace Gentor\Olx\Api;

class Credentials
{
    /**
     * @var int
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @param int $clientId
     * @param string $clientSecret
     */
    public function __construct(int $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }
}
