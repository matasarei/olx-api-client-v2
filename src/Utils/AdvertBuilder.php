<?php

namespace Gentor\Olx\Utils;

/**
 * Class AdvertBuilder
 *
 * @package Gentor\Olx\Utils
 */
class AdvertBuilder
{
    const TYPE_PRIVATE = 'private';

    /**
     * @var array
     */
    protected $data = [
        'advertiser_type' => self::TYPE_PRIVATE,
        'attributes' => []
    ];

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function addTitle(string $title)
    {
        return $this->set('title', $title);
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function addDescription(string $description)
    {
        return $this->set('description', $description);
    }

    /**
     * @param int $categoryId
     *
     * @return $this
     */
    public function addCategoryId(int $categoryId)
    {
        return $this->set('category_id', $categoryId);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function addType(string $type)
    {
        return $this->set('advertiser_type', $type);
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function addExternalUrl(string $url)
    {
        return $this->set('external_url', $url);
    }

    /**
     * @param string $externalId
     *
     * @return $this
     */
    public function addExternalId(string $externalId)
    {
        return $this->set('external_id', $externalId);
    }

    /**
     * @param string $name
     * @param string $phone
     *
     * @return $this
     */
    public function addContact(string $name, string $phone)
    {
        return $this->set('contact', [
            'name' => $name,
            'phone' => $phone
        ]);
    }

    /**
     * @param int $cityId
     * @param float|null $latitude
     * @param float|null $longitude
     *
     * @return $this
     */
    public function addLocation(int $cityId, float $latitude = null, float $longitude = null)
    {
        return $this->set('location', [
            'city_id' => $cityId,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
    }

    /**
     * @param int $price
     * @param string|null $currency
     * @return $this
     */
    public function addPrice(int $price, string $currency = null)
    {
        $value = [
            'value' => $price
        ];

        if (null !== $currency) {
            $value['currency'] = $currency;
        }

        return $this->set('price', $value);
    }

    /**
     * @param $code
     * @param $value
     *
     * @return $this
     */
    public function addAttribute($code, $value)
    {
        $this->data['attributes'][] = [
            'code' => $code,
            'value' => $value
        ];

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    protected function set(string $name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }
}
