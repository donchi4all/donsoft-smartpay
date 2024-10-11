<?php

namespace Donsoft\SmartPay\Tests;


use Donsoft\SmartPay\Services\Processors\ProcessorA;
use Donsoft\SmartPay\Services\Routing\PaymentRouter;
use Tests\TestCase; // Import Laravel's TestCase


class PaymentRouterTest extends TestCase
{
    protected $router;

    /**
     * Set up the test environment.
     * This method runs before each test to ensure a fresh instance of the router.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Mock configuration values for processors
        config()->set('smartpay.processors.processorA.cost_per_transaction', 0.5);
        config()->set('smartpay.processors.processorA.supported_currencies', ['USD', 'EUR']);
        config()->set('smartpay.processors.processorA.min_transaction_amount', 1);
        config()->set('smartpay.processors.processorA.reliability', 0.85);

        config()->set('smartpay.processors.processorB.cost_per_transaction', 1);
        config()->set('smartpay.processors.processorB.supported_currencies', ['USD']);
        config()->set('smartpay.processors.processorB.min_transaction_amount', 100);
        config()->set('smartpay.processors.processorB.reliability', 0.85);

        // Initialize the PaymentRouter
        $this->router = new PaymentRouter();
    }


    /**
     * Test that the router correctly routes a valid transaction.
     * It checks that the best processor is chosen based on the currency.
     */
    public function test_it_routes_to_best_processor_based_on_currency()
    {
        // Create a transaction with a valid currency
        $transaction = (object) ['amount' => 100, 'currency' => 'USD'];

        // Route the transaction and get the best processor
        $bestProcessor = $this->router->route($transaction);

        // Assert that the best processor is ProcessorA (the one supporting USD and EUR)
        $this->assertInstanceOf(ProcessorA::class, $bestProcessor);
    }

    /**
     * Test that transactions below the minimum amount are rejected.
     */
    public function test_it_rejects_transactions_below_minimum_amount()
    {
        // Create a transaction with an amount below the minimum required for ProcessorA
        $transaction = (object) ['amount' => 0.5, 'currency' => 'USD'];

        // Expect an exception when routing the transaction
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No suitable payment processors available.');

        // Try routing the transaction; this should throw an exception
        $this->router->route($transaction);
    }


    /**
     * Test error handling for invalid transaction amounts.
     */
    public function test_it_handles_invalid_transaction_amount()
    {
        // Create a transaction with an invalid amount (negative)
        $transaction = (object) ['amount' => -10, 'currency' => 'USD'];

        // Expect an exception when routing the transaction
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No suitable payment processors available.');

        // Route the transaction; this should throw an exception
        $this->router->route($transaction);
    }
    /**
     * Test successful transaction processing for ProcessorA.
     * This test checks that valid transactions are processed correctly.
     */
    public function test_it_processes_valid_transaction_with_processorA()
    {
        // Create a valid transaction
        $transaction = (object) ['amount' => 100, 'currency' => 'USD'];

        // Route the transaction
        $bestProcessor = $this->router->route($transaction);

        // Process the transaction and capture the result
        $result = $bestProcessor->process($transaction);

        // Assert that the result indicates success
        $this->assertEquals('success', $result['status']);
        $this->assertNotEmpty($result['transaction_id']);
    }

    /**
     * Test successful transaction processing for ProcessorB.
     * This test checks that valid transactions are processed correctly.
     */
    public function test_it_processes_valid_transaction_with_processorB()
    {
        // Create a valid transaction
        $transaction = (object) ['amount' => 100, 'currency' => 'USD'];

        // Route the transaction
        $bestProcessor = $this->router->route($transaction);

        // Process the transaction and capture the result
        $result = $bestProcessor->process($transaction);

        // Assert that the result indicates success
        $this->assertEquals('success', $result['status']);
        $this->assertNotEmpty($result['transaction_id']);
    }
}
