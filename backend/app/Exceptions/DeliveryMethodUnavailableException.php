<?php

namespace App\Exceptions;

use Exception;

class DeliveryMethodUnavailableException extends Exception
{
    public function __construct(public readonly string $method)
    {
        parent::__construct(ucfirst($method).' is currently unavailable.');
    }
}
