<?php

namespace Donsoft\SmartPay\Services\Processors;

class ProcessorA extends BaseProcessor
{
    public function __construct()
    {
        // Set properties using application config
        parent::__construct(
            'ProcessorA',
            config('smartpay.processors.processorA.cost_per_transaction', 0.5),
            config('smartpay.processors.processorA.supported_currencies', ['USD', 'EUR']),
            config('smartpay.processors.processorA.reliability', 0.85),
            config('smartpay.processors.processorA.min_transaction_amount', 1) // Minimum transaction amount
        );
    }

    /**
     * Process the transaction.
     *
     * @param object $transaction The transaction details.
     * @return array Result of the processing.
     */
    public function process($transaction)
    {
        try {
            $this->validateTransaction($transaction);
            return $this->successResponse($transaction);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }
}
