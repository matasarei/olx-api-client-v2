<?php

namespace Gentor\Olx\Api;

use GuzzleHttp\Exception\GuzzleException;

class Threads extends ApiResource
{
    public function getEndpoint(): string
    {
        return 'partner/threads';
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function list(int $advert_id = 0, int $interlocutor_id = 0, $limit = 0, $offset = 0): array
    {
        $data = [];

        if ($advert_id) {
            $data['advert_id'] = $advert_id;
        }

        if ($interlocutor_id) {
            $data['interlocutor_id'] = $interlocutor_id;
        }

        if ($limit > 0) {
            $data = array_merge($data, [
                'limit' => $limit,
                'offset' => $offset
            ]);
        }

        if (!empty($data)) {
            return $this->request('GET', $this->getEndpoint(), $data);
        }

        return $this->getAll();
    }

    /**
     * @throws OlxException
     * @throws GuzzleException
     */
    public function getMessages(int $threadId, $limit = 0, $offset = 0): array
    {
        $data = [];

        if ($limit > 0) {
            $data = [
                'limit' => $limit,
                'offset' => $offset
            ];
        }
        return $this->request('GET', sprintf('%s/%d/messages', $this->getEndpoint(), $threadId), $data);
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMessage(int $threadId, int $messageId): array
    {
        return $this->request(
            'GET',
            sprintf('%s/%d/messages/%d', $this->getEndpoint(), $threadId, $messageId)
        );
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reply(int $threadId, string $text, array $attachments = null): array
    {
        $data = ['text' => $text];

        if ($attachments) {
            $data['attachments'] = $attachments;
        }

        return $this->request('POST', sprintf('%s/%d/messages', $this->getEndpoint(), $threadId), $data);
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function markAsRead(int $threadId): array
    {
        return $this->request('POST', sprintf('%s/%d/commands', $this->getEndpoint(), $threadId), [
            'command' => 'mark-as-read',
        ]);
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setFavorite(int $threadId, bool $isFavorite): array
    {
        return $this->request('POST', sprintf('%s/%d/commands', $this->getEndpoint(), $threadId), [
            'command' => 'set-favourite',
            'is_favourite' => $isFavorite
        ]);
    }
}
