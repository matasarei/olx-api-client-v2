<?php

namespace Gentor\Olx\Utils;

class AdvertBuilder
{
    const TYPE_PRIVATE = 'private';

    protected $data = [
        'advertiser_type' => self::TYPE_PRIVATE,
        'attributes' => [],
        'images' => [],
    ];

    public function getData(): array
    {
        return $this->data;
    }

    public function addTitle(string $title): self
    {
        return $this->set('title', $title);
    }

    public function addDescription(string $description): self
    {
        return $this->set('description', $description);
    }

    public function addCategoryId(int $categoryId): self
    {
        return $this->set('category_id', $categoryId);
    }

    public function addType(string $type): self
    {
        return $this->set('advertiser_type', $type);
    }

    public function addExternalUrl(string $url): self
    {
        return $this->set('external_url', $url);
    }

    public function addExternalId(string $externalId): self
    {
        return $this->set('external_id', $externalId);
    }

    public function addContact(string $name, string $phone): self
    {
        return $this->set('contact', [
            'name' => $name,
            'phone' => $phone
        ]);
    }

    public function addLocation(
        int $cityId,
        int $districtId = null,
        float $latitude = null,
        float $longitude = null
    ): self {
        return $this->set('location', [
            'city_id' => $cityId,
            'district_id' => $districtId,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
    }

    public function addPrice(int $price, string $currency = null): self
    {
        $value = [
            'value' => $price
        ];

        if (null !== $currency) {
            $value['currency'] = $currency;
        }

        return $this->set('price', $value);
    }

    public function addAttribute($code, $value): self
    {
        $this->data['attributes'][] = [
            'code' => $code,
            'value' => $value
        ];

        return $this;
    }

    public function addImage(string $url): self
    {
        $this->data['images'][] = [
            'url' => $url
        ];

        return $this;
    }

    protected function set(string $name, $value): self
    {
        $this->data[$name] = $value;

        return $this;
    }
}
