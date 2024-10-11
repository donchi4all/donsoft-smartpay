<?php

namespace Donsoft\SmartPay\Contracts;

/**
 * Abstract class representing a generic payment processor.
 * All specific payment processors (e.g., ProcessorA, ProcessorB) must extend this class.
 */
abstract class PaymentProcessor
{
    // Processor name (e.g., 'ProcessorA')
    protected $name;

    // Cost per transaction (specific to the processor)
    protected $costPerTransaction;

    // Currencies supported by the processor
    protected $supportedCurrencies;

    // Reliability score for the processor
    protected $reliability;

    /**
     * Constructor to initialize the processor with a name, cost, supported currencies, and reliability.
     *
     * @param string $name Processor name (e.g., 'ProcessorA').
     * @param float $costPerTransaction Cost for processing a transaction.
     * @param array $supportedCurrencies Currencies this processor supports (e.g., ['USD', 'EUR']).
     * @param float $reliability Reliability score (between 0.0 and 1.0, where 1.0 is the most reliable).
     */
    public function __construct($name, $costPerTransaction, $supportedCurrencies, $reliability = 1.0)
    {
        $this->name = $name;
        $this->costPerTransaction = $costPerTransaction;
        $this->supportedCurrencies = $supportedCurrencies;
        $this->reliability = $reliability;  // Set reliability
    }

    /**
     * Get the name of the processor.
     *
     * @return string The name of the processor.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if the processor supports a given currency.
     *
     * @param string $currency The currency to check (e.g., 'USD').
     * @return bool True if the processor supports the currency, false otherwise.
     */
    public function supportsCurrency($currency)
    {
        return in_array($currency, $this->supportedCurrencies);
    }

    /**
     * Get the cost per transaction for this processor.
     *
     * @return float The cost per transaction.
     */
    public function getCostPerTransaction()
    {
        return $this->costPerTransaction;
    }

    /**
     * Get the reliability score of the processor.
     * The score is between 0.0 and 1.0, where 1.0 is the most reliable.
     *
     * @return float The reliability score.
     */
    public function getReliability()
    {
        return $this->reliability;
    }

    /**
     * Abstract method to process a transaction.
     * This method must be implemented by each specific processor.
     *
     * @param object $transaction The transaction details.
     * @return array The result of the transaction processing.
     */
    abstract public function process($transaction);
}
