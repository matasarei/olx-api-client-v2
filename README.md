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

## Testing and development
1. Install vendors
```bash
docker run --rm -v $(pwd):/app -w /app composer:lts composer install
```
2. Run tests
```bash
docker run --rm -v $(pwd):/app -w /app composer:lts vendor/bin/phpunit
```
