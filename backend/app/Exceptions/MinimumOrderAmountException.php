<?php

namespace App\Exceptions;

use Exception;

class MinimumOrderAmountException extends Exception
{
    public function __construct(public readonly float $minimum)
    {
        parent::__construct("The minimum order amount is RM".number_format($minimum, 2).'.');
    }
}
