# SmartPay

SmartPay is a Laravel package to intelligently route payment transactions to the most suitable payment processor based on factors like transaction cost, reliability, and currency support.

## Installation

You can install the package via composer:

```bash
composer require donsoft/smartpay
```


## Configuration

### Check Service Provider Registration:

Ensure that your SmartPayServiceProvider is correctly registered in your Laravel application. It should be included in the providers array in the config/app.php file:

```php
'providers' => [
    // Other Service Providers
    Donsoft\SmartPay\SmartPayServiceProvider::class,
],

```

You can publish the configuration file using the following Artisan command:

```bash
php artisan vendor:publish --provider="Donsoft\\SmartPay\\Providers\\SmartPayServiceProvider"
```

The configuration file will be located at config/smartpay.php. You can modify it to define your payment processors and currencies.

## Testing
Run the tests with:

```bash
vendor/bin/phpunit

vendor/bin/phpunit packages/Donsoft/SmartPay/tests/PaymentRouterTest.php
```
