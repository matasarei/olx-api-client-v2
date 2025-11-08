# OLX API Client v2
![CI workflow](https://github.com/matasarei/olx-api-client-v2/actions/workflows/tests.yml/badge.svg)

This package implements PHP client for OLX Partner API.

## Installation
To install the package to your project via [Composer](http://getcomposer.org/) simply run:
```bash
composer require matasarei/olx-api-client-v2
```

## Documentation
Official OLX API documentation and developers portal:
* [Bulgaria](https://developer.olx.bg/api/doc)
* [Kazakhstan](https://developer.olx.kz/api/doc)
* [Poland](https://developer.olx.pl/api/doc)
* [Portugal](https://developer.olx.pt/api/doc)
* [Romania](https://developer.olx.ro/api/doc)
* [Ukraine](https://developer.olx.ua/api/doc)

Check the troubleshooting section if you have any issues.

## Usage

### Basic Example
```php
use Gentor\Olx\Api\Client;
use Gentor\Olx\Api\Credentials;

$credentials = new Credentials('your_client_id', 'your_client_secret');
$client = new Client($credentials, Client::OLX_UA);

// Create an advert
$response = $client->adverts()->create([
    'title' => 'My Product',
    'description' => 'Product description...',
    'category_id' => 123,
    // ... other required fields
]);

// Access the created advert data
$advertData = $response['data'];
echo "Created advert ID: " . $advertData['id'];
echo "Status: " . $advertData['status'];
```

### Important: API Response Format
All OLX API responses wrap the actual data in a `data` key according to the official API specification:

```php
// What you get from the API:
[
  'data' => [
    'id' => 905890605,
    'status' => 'active',
    // ... other advert fields
  ]
]

// Access the actual data:
$response = $client->adverts()->create($request);
$advertData = $response['data'];
```

This applies to all endpoints:
- `GET /adverts` returns `['data' => [array of adverts]]`
- `POST /adverts` returns `['data' => {advert object}]`
- `GET /adverts/{id}` returns `['data' => {advert object}]`
- `PUT /adverts/{id}` returns `['data' => {advert object}]`

## Testing and development
1. Install vendors
```bash
docker run --rm -v $(pwd):/app -w /app composer:lts composer install
```
2. Run tests
```bash
docker run --rm -v $(pwd):/app -w /app composer:lts vendor/bin/phpunit
```
