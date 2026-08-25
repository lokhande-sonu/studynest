<?php

namespace App\Enums;

class OrderPaymentMode
{
    const COD = 1;
    const ONLINE = 2;

    public static function label($mode)
    {
        return match ((int) $mode) {
            self::COD => 'Cash on Delivery',
            self::ONLINE => 'Online Payment',
            default => 'Unknown',
        };
    }

    public static function badge($mode)
    {
        return match ((int) $mode) {
            self::COD => 'warning',
            self::ONLINE => 'info',
            default => 'secondary',
        };
    }
}
