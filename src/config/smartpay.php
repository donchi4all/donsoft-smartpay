<?php

return [

    /*
   |--------------------------------------------------------------------------
   | Priority Weights for Payment Processor Selection
   |--------------------------------------------------------------------------
   | The user can define the importance of different factors like currency support,
   | reliability, and transaction cost by assigning weights. Higher values indicate
   | higher priority.
   | 1: Least important 2: Second most important 3:Most important factor
   */
    'priorities' => [
        'currency_support' => 3,  // Most important factor
        'reliability' => 2,       // Second most important
        'transaction_cost' => 1,  // Least important
    ],

    /*
   |--------------------------------------------------------------------------
   | Supported Payment Processors
   |--------------------------------------------------------------------------
   | The user can define new payment processors here. Simply add the class
   | name of the processor and its configuration. Make sure each processor
   | class extends the PaymentProcessor abstract class.
   */

    'processors' => [
        'processorA' => [
            'class' => \Donsoft\SmartPay\Services\Processors\ProcessorA::class,
            'cost_per_transaction' => 0.5,
            'supported_currencies' => ['USD', 'EUR'],
            'min_transaction_amount' => 0.1,  // Example additional configuration
            'reliability' => 0.85,  // Reliability score out of 1.0
        ],
        'processorB' => [
            'class' => \Donsoft\SmartPay\Services\Processors\ProcessorB::class,
            'cost_per_transaction' => 0.3,
            'supported_currencies' => ['USD'],
            'min_transaction_amount' => 100,  // Example additional configuration
            'reliability' => 0.95,  // Reliability score out of 1.0
        ],
    ],


    'default_currency' => 'USD',

];

