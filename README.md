# OLX API Client v2

[![CI workflow](https://github.com/matasarei/olx-api-client-v2/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/matasarei/olx-api-client-v2/actions/workflows/tests.yml)
[![Latest stable version](https://poser.pugx.org/matasarei/olx-api-client-v2/v)](https://packagist.org/packages/matasarei/olx-api-client-v2)
[![Total downloads](https://poser.pugx.org/matasarei/olx-api-client-v2/downloads)](https://packagist.org/packages/matasarei/olx-api-client-v2)
[![PHP version](https://poser.pugx.org/matasarei/olx-api-client-v2/require/php)](https://packagist.org/packages/matasarei/olx-api-client-v2)
[![License](https://poser.pugx.org/matasarei/olx-api-client-v2/license)](LICENSE)

This package implements PHP client for OLX Partner API.

## Requirements
* PHP 7.4 or newer — CI runs the suite on 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5
* [Guzzle](https://docs.guzzlephp.org/) 7 or 8 — either works, and both are covered by CI
* `ext-json`

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

This response format applies to advert-related endpoints, for example:
- `GET /adverts` returns `['data' => [array of adverts]]`
- `POST /adverts` returns `['data' => {advert object}]`
- `GET /adverts/{id}` returns `['data' => {advert object}]`
- `PUT /adverts/{id}` returns `['data' => {advert object}]`

Other endpoints may have different response structures. Please refer to the official OLX API documentation for details on the response format of each endpoint.

## Testing and development
The commands below run through Docker, so no local PHP install is needed.

Use the `composer:2` image rather than `composer:lts`. The LTS image ships Composer 2.2, which
predates security-advisory checking — it installs dependencies with known vulnerabilities without
saying a word, which is how [#9](https://github.com/matasarei/olx-api-client-v2/issues/9) went
unnoticed locally while consumers could not install the package at all.

1. Install vendors
```bash
docker run --rm -v $(pwd):/app -w /app composer:2 composer update
```
2. Run tests
```bash
docker run --rm -v $(pwd):/app -w /app composer:2 vendor/bin/phpunit
```
3. Check for vulnerable dependencies
```bash
docker run --rm -v $(pwd):/app -w /app composer:2 composer audit --no-dev
```

### Reproducing the CI dependency matrix
`composer update` on its own only ever resolves the newest allowed versions, which for
`^7.4 || ^8.0` always means Guzzle 8. CI additionally proves the floor of every constraint and the
Guzzle 7 path, and you can run either locally:

```bash
# Floor of every constraint (currently Guzzle 7.15.1 + psr7 2.13.0)
docker run --rm -v $(pwd):/app -w /app composer:2 composer update --prefer-lowest --prefer-stable

# The Guzzle 7 half of the constraint
docker run --rm -v $(pwd):/app -w /app composer:2 \
  composer update --with "guzzlehttp/guzzle:^7.15" --with "guzzlehttp/psr7:^2.13"
```

Note that `composer update` is used throughout rather than `composer install`: only re-resolving
applies Composer's security-advisory policy, so a dependency that has gone vulnerable fails loudly
instead of silently installing.

### Testing against a specific PHP version
Dependencies have to be resolved *for* the target version, otherwise Composer picks packages
requiring a newer PHP and the suite dies on a syntax error rather than a real failure. Pin the
platform, run the suite, then restore `composer.json`:

```bash
docker run --rm -v $(pwd):/app -w /app composer:2 composer config platform.php 7.4.33
docker run --rm -v $(pwd):/app -w /app composer:2 composer update
docker run --rm -v $(pwd):/app -w /app php:7.4-cli php vendor/bin/phpunit
git checkout composer.json   # drops the temporary platform pin
```
