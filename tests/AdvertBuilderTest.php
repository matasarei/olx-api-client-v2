<?php

use Gentor\Olx\Utils\AdvertBuilder;
use PHPUnit\Framework\TestCase;

class AdvertBuilderTest extends TestCase
{
    public function testAdvertBuilder()
    {
        $builder = new AdvertBuilder();

        $builder->addTitle('Title')
            ->addDescription('Description')
            ->addCategoryId(1)
            ->addType('private')
            ->addExternalUrl('http://external.com')
            ->addExternalId('123')
            ->addContact('Name', '123456789')
            ->addLocation(1, 2, 1.23, 4.56)
            ->addPrice(100, 'USD')
            ->addAttribute('code', 'value')
            ->addImage('http://image.com/image.png');

        $expected = [
            'advertiser_type' => 'private',
            'attributes' => [
                ['code' => 'code', 'value' => 'value']
            ],
            'images' => [
                ['url' => 'http://image.com/image.png']
            ],
            'title' => 'Title',
            'description' => 'Description',
            'category_id' => 1,
            'external_url' => 'http://external.com',
            'external_id' => '123',
            'contact' => ['name' => 'Name', 'phone' => '123456789'],
            'location' => ['city_id' => 1, 'district_id' => 2, 'latitude' => 1.23, 'longitude' => 4.56],
            'price' => ['value' => 100, 'currency' => 'USD']
        ];

        $this->assertEquals($expected, $builder->getData());
    }
}
