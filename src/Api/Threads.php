<?php


namespace Gentor\Olx\Api;

/**
 * Class Threads
 *
 * @package Gentor\Olx\Api
 */
class Threads extends ApiResource
{
    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return 'partner/threads';
    }

	/**
	 * @param int $advert_id
	 * @param int $interlocutor_id
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array|mixed|null
	 *
	 * @throws OlxException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function list(int $advert_id = 0, int $interlocutor_id = 0, $limit = 0, $offset = 0)
	{
		$data = [];

		if ($advert_id) $data['advert_id'] = $advert_id;
		if ($interlocutor_id) $data['interlocutor_id'] = $interlocutor_id;

		if ($limit > 0) {
			$data = array_merge($data, [
				'limit'           => $limit,
				'offset'          => $offset
			]);
		}

		if (!empty($data)) {
			return $this->request('GET', $this->getEndpoint(), $data);
		}

		return $this->getAll();
	}

	/**
	 * @param int $threadId
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array|null
	 *
	 * @throws OlxException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getMessages(int $threadId, $limit = 0, $offset = 0)
	{
		$data = [];

		if ($limit > 0) {
			$data = [
				'limit'           => $limit,
				'offset'          => $offset
			];
		}
		return $this->request('GET', sprintf('%s/%d/messages', $this->getEndpoint(), $threadId), $data);
	}

	/**
	 * @param int $threadId
	 * @param int $messageId
	 *
	 * @return array|null
	 *
	 * @throws OlxException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getMessage(int $threadId, int $messageId)
	{
		return $this->request('GET', sprintf('%s/%d/messages/%d', $this->getEndpoint(), $threadId, $messageId));
	}

	/**
	 * @param int $threadId
	 * @param string $text
	 * @param string[]|null $attachments - url's
	 *
	 * @return array|null
	 *
	 * @throws OlxException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function reply(int $threadId, string $text, array $attachments = null)
	{
		$data = [
			'text' => $text
		];

		if ($attachments) {
			//$data['attachments'] = json_encode($attachments);
			$data['attachments'] = $attachments;
		}

		return $this->request('POST', sprintf('%s/%d/messages', $this->getEndpoint(), $threadId), $data);
	}

	/**
	 * @param int $threadId
	 * @return array|null
	 *
	 * @throws OlxException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function markAsRead(int $threadId)
	{
		return $this->request('POST', sprintf('%s/%d/commands', $this->getEndpoint(), $threadId), [
			'command' => 'mark-as-read',
		]);
	}

	/**
	 * @param int $threadId
	 * @param bool $isFavorite
	 *
	 * @return array|null
	 *
	 * @throws OlxException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function setFavorite(int $threadId, bool $isFavorite)
	{
		return $this->request('POST', sprintf('%s/%d/commands', $this->getEndpoint(), $threadId), [
			'command' => 'set-favourite',
			'is_favourite' => $isFavorite
		]);
	}
}