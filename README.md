
# Donsoft SmartPay

**Donsoft SmartPay** is a Laravel package that intelligently routes payment transactions to the most suitable payment processor based on configurable factors such as transaction cost, reliability, and currency support.

## Features
- **Multiple Payment Processor Support**: Supports different payment processors with configurable parameters.
- **Intelligent Routing**: Routes transactions to the best processor based on defined priorities (cost, reliability, currency support).
- **Customizable**: Easily add new processors or modify existing ones.
- **Extensible**: Allows developers to extend processors and define custom behavior.
  
## Installation

### 1. Install the package via Composer:

```bash
composer require donsoft/smartpay
```

### 2. Publish the configuration file:

```bash
php artisan vendor:publish --tag=config --provider="Donsoft\\SmartPay\\Providers\\SmartPayServiceProvider"
```

This will create a `config/smartpay.php` file where you can configure your payment processors, priorities, and default settings.

### 3. Add Service Provider (for Laravel versions below 5.5):

If you’re using a Laravel version below 5.5, add the following service provider to your `config/app.php`:

```php
'providers' => [
    Donsoft\\SmartPay\\Providers\\SmartPayServiceProvider::class,
],
```

## Configuration

The configuration file `smartpay.php` allows you to define supported payment processors, the priority of routing factors, and more. Example configuration:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Priority Weights for Payment Processor Selection
    |--------------------------------------------------------------------------
    */
    'priorities' => [
        'currency_support' => 3,  // Most important
        'reliability' => 2,       // Second most important
        'transaction_cost' => 1,  // Least important
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Payment Processors
    |--------------------------------------------------------------------------
    */
    'processors' => [
        'processorA' => [
            'class' => \\Donsoft\\SmartPay\\Services\\Processors\\ProcessorA::class,
            'cost_per_transaction' => 0.5,
            'supported_currencies' => ['USD', 'EUR'],
            'min_transaction_amount' => 1,
            'reliability' => 0.85,
        ],
        'processorB' => [
            'class' => \\Donsoft\\SmartPay\\Services\\Processors\\ProcessorB::class,
            'cost_per_transaction' => 1.0,
            'supported_currencies' => ['USD'],
            'min_transaction_amount' => 100,
            'reliability' => 0.90,
        ],
    ],

    'default_currency' => 'USD',
];
```

## Usage

This is how to use **Donsoft SmartPay** in your Laravel project:

### 1. Inject the Payment Router into a Controller

You can inject the **PaymentRouter** service into your controller and use it to route transactions to the appropriate processor.

```php
use Donsoft\\SmartPay\\Services\\Routing\\PaymentRouter;
use Illuminate\\Http\\Request;

class PaymentController extends Controller
{
    protected $paymentRouter;

    public function __construct(PaymentRouter $paymentRouter)
    {
        $this->paymentRouter = $paymentRouter;
    }

    public function processPayment(Request $request)
    {
        $transaction = (object) [
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency', config('smartpay.default_currency')),
        ];

        try {
            $processor = $this->paymentRouter->route($transaction);
            $response = $processor->process($transaction);

            return response()->json($response);
        } catch (\\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
```

### 2. Example Request to Process Payment:

```bash
POST /api/process-payment
Content-Type: application/json

{
    "amount": 150,
    "currency": "USD"
}
```

This will route the payment to the best processor based on the configuration and transaction details.

## Extending the Processor

You can add custom payment processors by extending the base processor class (**`BaseProcessor.php`**) and adding it to the configuration.

### Step 1: Create Your Custom Processor

```php
namespace App\\Services\\Processors;

use Donsoft\\SmartPay\\Services\\Processors\\BaseProcessor;

class CustomProcessor extends BaseProcessor
{
    public function __construct()
    {
        parent::__construct(
            'CustomProcessor',
            0.25,  // Cost per transaction
            ['USD', 'GBP'],  // Supported currencies
            0.95,  // Reliability
            10  // Minimum transaction amount
        );
    }

    public function process($transaction)
    {
        // Custom processing logic here
        return $this->successResponse($transaction);
    }
}
```

### Step 2: Add Your Processor to the Configuration

```php
return [
    'processors' => [
        'customProcessor' => [
            'class' => \\App\\Services\\Processors\\CustomProcessor::class,
            'cost_per_transaction' => 0.25,
            'supported_currencies' => ['USD', 'GBP'],
            'min_transaction_amount' => 10,
            'reliability' => 0.95,
        ],
    ],
];
```

Now, **CustomProcessor** will be included in the intelligent routing when a payment is processed.

## Customizing the Configuration

You can adjust the **priorities** and **processors** directly in the `smartpay.php` config file:

- **Priorities**: Adjust the weight of each factor in the routing decision (currency support, reliability, transaction cost).
  
- **Processors**: Define new or existing payment processors and their configurations (cost per transaction, supported currencies, minimum transaction amounts).

```php
'priorities' => [
    'currency_support' => 4,  // Higher priority
    'reliability' => 2,
    'transaction_cost' => 1,
],
```

## Running Tests

The package includes tests to verify its functionality. To run the tests:

```bash
vendor/bin/phpunit
```

Make sure your test classes are under the **`tests/`** directory.

---

## License

This package is open-source software licensed under the [MIT license](LICENSE).

---

### Conclusion

This **Donsoft SmartPay** package provides a flexible, customizable solution for routing payment transactions in Laravel applications. By allowing easy configuration and extensibility, developers can adapt it to suit their specific business logic and requirements.

If you have any questions or need support, feel free to reach out to the author
``` 

