<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Baking = 'baking';
    case Packing = 'packing';
    case OutForDelivery = 'out_for_delivery';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    /**
     * @return array<int, self>
     */
    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::Pending => [self::Preparing, self::Rejected, self::Cancelled],
            self::Preparing => [self::Baking, self::Cancelled],
            self::Baking => [self::Packing, self::Cancelled],
            self::Packing => [self::OutForDelivery, self::Cancelled],
            self::OutForDelivery => [self::Completed],
            self::Completed, self::Rejected, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNextStatuses(), true);
    }
}
