<?php

namespace Donsoft\SmartPay\Services\Processors;

use Donsoft\SmartPay\Contracts\PaymentProcessor;
use Donsoft\SmartPay\Exceptions\PaymentException;
use Illuminate\Support\Facades\Log;

abstract class BaseProcessor extends PaymentProcessor
{
    protected $minTransactionAmount;

    public function __construct($name, $costPerTransaction, $supportedCurrencies, $reliability, $minTransactionAmount)
    {
        parent::__construct($name, $costPerTransaction, $supportedCurrencies, $reliability);
        $this->minTransactionAmount = $minTransactionAmount;
    }

    /**
     * Get the minimum transaction amount for this processor.
     *
     * @return float
     */
    public function getMinTransactionAmount()
    {
        return $this->minTransactionAmount;
    }

    /**
     * Validate the transaction amount.
     *
     * @param object $transaction The transaction details.
     * @throws \Exception If the transaction amount is invalid or below the minimum.
     */
    protected function validateTransaction($transaction)
    {
        if (empty($transaction->amount) || $transaction->amount <= 0) {
            throw new PaymentException('Invalid transaction amount');
        }

        if ($transaction->amount < $this->getMinTransactionAmount()) {
            throw new PaymentException('Transaction amount is below the minimum required amount of ' . $this->getMinTransactionAmount());
        }
    }

    /**
     * Log and return a successful processing response.
     *
     * @param object $transaction The transaction details.
     * @return array
     */
    protected function successResponse($transaction)
    {
        Log::info("Processing transaction with {$this->getName()}: " . json_encode($transaction));
        return [
            'status' => 'success',
            'processor' => $this->getName(),
            'transaction_id' => uniqid('txn_')
        ];
    }

    /**
     * Log the error and return an error response.
     *
     * @param \Exception $e
     * @return array
     */
    protected function errorResponse(\Exception $e)
    {
        Log::error("{$this->getName()} Error: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
