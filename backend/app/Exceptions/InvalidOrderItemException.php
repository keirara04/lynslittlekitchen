<?php

namespace App\Exceptions;

use Exception;

class InvalidOrderItemException extends Exception
{
    public static function variantMismatch(int $variantId, int $productId): self
    {
        return new self("Variant #{$variantId} does not belong to product #{$productId}.");
    }
}
