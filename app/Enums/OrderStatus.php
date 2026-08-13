<?php

namespace App\Enums;

class OrderStatus
{
    const CANCELLED = 0;
    const PENDING = 1;
    const CONFIRMED = 2;
    const DELIVERED = 3;

    public static function label($status)
    {
        return match ((int) $status) {
            self::CANCELLED => 'Cancelled',
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::DELIVERED => 'Delivered',
            default => 'Unknown',
        };
    }

    public static function badge($status)
    {
        return match ((int) $status) {
            self::CANCELLED => 'danger',
            self::PENDING => 'warning',
            self::CONFIRMED => 'primary',
            self::DELIVERED => 'success',
            default => 'secondary',
        };
    }
}
