<?php

namespace Donsoft\SmartPay\Exceptions;

use Exception;

class PaymentException extends Exception
{
    // You can add additional properties or methods if needed

    /**
     * Create a new PaymentException instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "Payment processing error", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
