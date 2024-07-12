<?php

namespace Gentor\Olx\Api;

class Credentials
{
    protected int $clientId;
    protected string $clientSecret;

    public function __construct(int $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}
