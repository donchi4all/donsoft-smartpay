<?php

namespace Donsoft\SmartPay\Services\Routing;
use Donsoft\SmartPay\Exceptions\PaymentException;

class PaymentRouter
{
    /**
     * Route the transaction to the best payment processor based on priorities.
     *
     * @param object $transaction The transaction object containing relevant details.
     * @return PaymentProcessor The selected payment processor.
     * @throws \Exception If no suitable processor is found.
     */
    public function route($transaction)
    {
        // Step 1: Filter processors based on currency support and minimum transaction amount.
        $suitableProcessors = $this->filterProcessorsByCriteria($transaction->currency, $transaction->amount);

        // Step 2: Choose the best processor by calculating the weighted score.
        return $this->chooseBestProcessor($suitableProcessors, $transaction);
    }

    /**
     * Filter processors based on supported currency and minimum transaction amount.
     *
     * @param string $currency The currency of the transaction.
     * @param float $amount The transaction amount.
     * @return array Filtered list of suitable processors.
     */
    protected function filterProcessorsByCriteria($currency, $amount)
    {
        $processors = config('smartpay.processors');
        $suitableProcessors = [];

        // Iterate over each processor to check if it supports the currency and meets the minimum amount
        foreach ($processors as $processorConfig) {
            $processor = new $processorConfig['class'](); // Instantiate the processor

            // Check if processor supports the transaction currency and meets the min transaction amount
            if ($processor->supportsCurrency($currency) && $amount >= $processor->getMinTransactionAmount()) {
                $suitableProcessors[] = $processor;
            }
        }

        return $suitableProcessors;
    }

    /**
     * Choose the best processor based on currency support, reliability, and transaction cost.
     *
     * @param array $processors Array of suitable processors.
     * @param object $transaction The transaction object.
     * @return PaymentProcessor The best processor based on weighted score.
     * @throws \Exception If no suitable processors are found.
     */
    protected function chooseBestProcessor($processors, $transaction)
    {
        $bestProcessor = null;
        $highestScore = -1;

        // Iterate through the filtered list of processors
        foreach ($processors as $processor) {
            // Calculate the score for each processor based on defined priorities
            $score = $this->calculateProcessorScore($processor, $transaction);
            \Illuminate\Support\Facades\Log::info("Evaluating processor: " . get_class($processor) . " with score: " . $score);

            // Select the processor with the highest score
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestProcessor = $processor;
            }
        }

        // If no processor is found, throw an exception
        if (!$bestProcessor) {
            throw new PaymentException('No suitable payment processors available.');
        }

        return $bestProcessor;  // Return the processor with the highest score
    }

    /**
     * Calculate the weighted score for a processor based on priorities.
     *
     * @param PaymentProcessor $processor The payment processor object.
     * @param object $transaction The transaction details.
     * @return float The calculated score for the processor.
     */
    protected function calculateProcessorScore($processor, $transaction)
    {
        // Fetch the priority weights from configuration, with default fallback values
        $priorities = config('smartpay.priorities', [
            'currency_support' => 3,   // Currency support is the most important
            'reliability' => 2,        // Reliability is the second most important
            'transaction_cost' => 1,   // Transaction cost is the least important
        ]);
        \Illuminate\Support\Facades\Log::info("Loaded priorities: ", $priorities);

        // Initialize the score to zero
        $score = 0;

        // 1. Currency Support: If the processor supports the transaction currency, add the full weight
        if ($processor->supportsCurrency($transaction->currency)) {
            $score += $priorities['currency_support'];
        }

        // 2. Reliability: Add the processor's reliability score, weighted by the reliability priority
        $score += $processor->getReliability() * $priorities['reliability'];

        // 3. Transaction Cost: Lower transaction cost gets a higher score (inverse scaling)
        // If cost is 1, score is 0; if cost is 0, score is maximum
        $costScore = max(0, 1.0 - $processor->getCostPerTransaction());
        $score += $costScore * $priorities['transaction_cost'];

        // Return the total score for the processor
        return $score;
    }


}



// namespace Donsoft\SmartPay;

// class PaymentRouter
// {
//     protected $processors = [];

//     /**
//      * Constructor.
//      * Dynamically load payment processors from the configuration file.
//      */
//     public function __construct()
//     {
//         // Retrieve the configured processors, defaulting to an empty array if not set
//         $configuredProcessors = config('smartpay.processors', []);

//         // Check if processors are set and initialize them
//         foreach ($configuredProcessors as $processorConfig) {
//             if (isset($processorConfig['class']) && class_exists($processorConfig['class'])) {
//                 $this->processors[] = new $processorConfig['class']();
//             }
//         }

//         // Check if no processors were loaded
//         if (empty($this->processors)) {
//             throw new \Exception('No payment processors configured.');
//         }
//     }

//     /**
//      * Route a transaction to the best payment processor.
//      */
//     public function route($transaction)
//     {
//         $currency = $transaction->currency ?? config('smartpay.default_currency');
//         $suitableProcessors = $this->filterProcessorsByCriteria($currency);
//         return $this->chooseBestProcessor($suitableProcessors, $transaction);
//     }


//     /**
//      * Filter processors by supported currency.
//      */
//     protected function filterProcessorsByCriteria($currency)
//     {
//         return array_filter($this->processors, function ($processor) use ($currency) {
//             return $processor->supportsCurrency($currency);
//         });
//     }


//     // protected function chooseBestProcessor($processors)
//     // {
//     //     // If no suitable processors, return null or throw an exception
//     //     if (empty($processors)) {
//     //         throw new \Exception('No suitable payment processors available.');
//     //     }

//     //     // Logic to select the best processor based on cost
//     //     // Start with the first processor as the best
//     //     $bestProcessor = array_shift($processors); // Take the first processor

//     //     // Iterate over remaining processors to find a cheaper one
//     //     foreach ($processors as $processor) {
//     //         // Compare the cost per transaction
//     //         if ($processor->getCostPerTransaction() < $bestProcessor->getCostPerTransaction()) {
//     //             $bestProcessor = $processor; // Update best processor if a cheaper one is found
//     //         }
//     //     }

//     //     return $bestProcessor; // Return the best processor based on cost
//     // }

//     protected function chooseBestProcessor($processors, $transaction)
//     {
//         $suitableProcessors = [];

//         foreach ($processors as $processor) {
//             if ($processor->supportsCurrency($transaction->currency) &&
//                 $transaction->amount >= $processor->getMinTransactionAmount()) {
//                 $suitableProcessors[] = $processor;
//             }
//         }

//         // If no suitable processors, throw an exception
//         if (empty($suitableProcessors)) {
//             throw new \Exception('No suitable payment processors available for the given transaction.');
//         }

//         // Sort processors by reliability first, then cost
//         usort($suitableProcessors, function ($a, $b) {
//             if ($a->getReliability() === $b->getReliability()) {
//                 return $a->getCostPerTransaction() <=> $b->getCostPerTransaction();
//             }
//             return $b->getReliability() <=> $a->getReliability(); // Higher reliability first
//         });

//         return $suitableProcessors[0]; // Return the most reliable and cheapest processor
//     }

//     protected function calculateProcessorScore($processor, $transaction)
//     {
//         $priorities = config('smartpay.priorities', [
//             'currency_support' => 3,
//             'reliability' => 2,
//             'transaction_cost' => 1
//         ]);

//         $score = 0;

//         // 1. Score based on currency support
//         if ($processor->supportsCurrency($transaction->currency)) {
//             $score += $priorities['currency_support'];
//         }

//         // 2. Score based on reliability
//         $score += $processor->getReliability() * $priorities['reliability']; // Weighted by reliability

//         // 3. Score based on transaction cost (lower cost gets higher score)
//         $maxCost = 1.0; // Assume the maximum cost could be 1.0 for simplicity
//         $score += ($maxCost - $processor->getCostPerTransaction()) * $priorities['transaction_cost'];

//         return $score;
//     }

// }
