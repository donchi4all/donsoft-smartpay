<?php

namespace Donsoft\SmartPay\Services\Processors;

class ProcessorB extends BaseProcessor
{
    public function __construct()
    {
        // Set properties using application config
        parent::__construct(
            'ProcessorB',
            config('smartpay.processors.processorB.cost_per_transaction', 1),
            config('smartpay.processors.processorB.supported_currencies', ['USD']),
            config('smartpay.processors.processorB.reliability', 0.85),
            config('smartpay.processors.processorB.min_transaction_amount', 100) // Minimum transaction amount
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
