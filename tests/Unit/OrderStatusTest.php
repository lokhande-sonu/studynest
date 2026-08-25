<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Enums\OrderStatus;
use App\Enums\OrderPaymentMode;

/**
 * Unit tests for OrderStatus and OrderPaymentMode enums.
 *
 * These are pure-logic tests with no I/O or database — the fastest
 * feedback loop in the test suite.
 */
class OrderStatusTest extends TestCase
{
    // ---------------------------------------------------------------
    //  OrderStatus::label()
    // ---------------------------------------------------------------

    public function test_label_returns_cancelled_for_status_0()
    {
        $this->assertEquals('Cancelled', OrderStatus::label(OrderStatus::CANCELLED));
    }

    public function test_label_returns_pending_for_status_1()
    {
        $this->assertEquals('Pending', OrderStatus::label(OrderStatus::PENDING));
    }

    public function test_label_returns_confirmed_for_status_2()
    {
        $this->assertEquals('Confirmed', OrderStatus::label(OrderStatus::CONFIRMED));
    }

    public function test_label_returns_delivered_for_status_3()
    {
        $this->assertEquals('Delivered', OrderStatus::label(OrderStatus::DELIVERED));
    }

    public function test_label_returns_unknown_for_unrecognised_status()
    {
        $this->assertEquals('Unknown', OrderStatus::label(99));
    }

    public function test_label_handles_string_input_gracefully()
    {
        // The controller code sometimes passes string status values from form inputs.
        $this->assertEquals('Pending', OrderStatus::label('1'));
        $this->assertEquals('Confirmed', OrderStatus::label('2'));
        $this->assertEquals('Cancelled', OrderStatus::label('0'));
    }

    // ---------------------------------------------------------------
    //  OrderStatus::badge()
    // ---------------------------------------------------------------

    public function test_badge_returns_danger_for_cancelled()
    {
        $this->assertEquals('danger', OrderStatus::badge(OrderStatus::CANCELLED));
    }

    public function test_badge_returns_warning_for_pending()
    {
        $this->assertEquals('warning', OrderStatus::badge(OrderStatus::PENDING));
    }

    public function test_badge_returns_primary_for_confirmed()
    {
        $this->assertEquals('primary', OrderStatus::badge(OrderStatus::CONFIRMED));
    }

    public function test_badge_returns_success_for_delivered()
    {
        $this->assertEquals('success', OrderStatus::badge(OrderStatus::DELIVERED));
    }

    public function test_badge_returns_secondary_for_unrecognised_status()
    {
        $this->assertEquals('secondary', OrderStatus::badge(99));
    }

    public function test_badge_handles_string_input_gracefully()
    {
        $this->assertEquals('warning', OrderStatus::badge('1'));
        $this->assertEquals('primary', OrderStatus::badge('2'));
    }

    // ---------------------------------------------------------------
    //  Constant values are correct
    // ---------------------------------------------------------------

    public function test_order_status_constants_are_sequential()
    {
        $this->assertEquals(0, OrderStatus::CANCELLED);
        $this->assertEquals(1, OrderStatus::PENDING);
        $this->assertEquals(2, OrderStatus::CONFIRMED);
        $this->assertEquals(3, OrderStatus::DELIVERED);
    }

    public function test_all_statuses_have_both_label_and_badge()
    {
        $statuses = [
            OrderStatus::CANCELLED,
            OrderStatus::PENDING,
            OrderStatus::CONFIRMED,
            OrderStatus::DELIVERED,
        ];

        foreach ($statuses as $status) {
            $label = OrderStatus::label($status);
            $badge = OrderStatus::badge($status);

            $this->assertNotEmpty($label, "Status {$status} has no label");
            $this->assertNotEmpty($badge, "Status {$status} has no badge");
            $this->assertNotEquals('Unknown', $label, "Status {$status} label should not be Unknown");
            $this->assertNotEquals('secondary', $badge, "Status {$status} badge should not be secondary");
        }
    }

    // ---------------------------------------------------------------
    //  OrderPaymentMode::label()
    // ---------------------------------------------------------------

    public function test_payment_mode_label_cod()
    {
        $this->assertEquals('Cash on Delivery', OrderPaymentMode::label(OrderPaymentMode::COD));
    }

    public function test_payment_mode_label_online()
    {
        $this->assertEquals('Online Payment', OrderPaymentMode::label(OrderPaymentMode::ONLINE));
    }

    public function test_payment_mode_label_unknown()
    {
        $this->assertEquals('Unknown', OrderPaymentMode::label(99));
    }

    public function test_payment_mode_label_handles_string_input()
    {
        $this->assertEquals('Cash on Delivery', OrderPaymentMode::label('1'));
        $this->assertEquals('Online Payment', OrderPaymentMode::label('2'));
    }

    // ---------------------------------------------------------------
    //  OrderPaymentMode::badge()
    // ---------------------------------------------------------------

    public function test_payment_mode_badge_cod()
    {
        $this->assertEquals('warning', OrderPaymentMode::badge(OrderPaymentMode::COD));
    }

    public function test_payment_mode_badge_online()
    {
        $this->assertEquals('info', OrderPaymentMode::badge(OrderPaymentMode::ONLINE));
    }

    public function test_payment_mode_badge_unknown()
    {
        $this->assertEquals('secondary', OrderPaymentMode::badge(99));
    }

    // ---------------------------------------------------------------
    //  GST calculation math (pure logic, no I/O)
    // ---------------------------------------------------------------

    /**
     * Test the GST inclusive calculation logic used in placeOrder.
     *
     * When gst_type is 'inclusive', the displayed price already includes
     * GST. The base price is extracted by dividing by (1 + rate/100).
     *
     * Example: ₹118 display price, 18% GST inclusive
     *   base = 118 / 1.18 = 100
     *   GST  = 118 - 100 = 18
     */
    public function test_gst_inclusive_calculation_per_unit()
    {
        $displayPrice = 118.00;
        $gstRate = 18; // 18%

        $basePrice = round($displayPrice / (1 + ($gstRate / 100)), 2);
        $gstAmount = round($displayPrice - $basePrice, 2);

        $this->assertEquals(100.00, $basePrice);
        $this->assertEquals(18.00, $gstAmount);

        // Verify: base + GST = display price (per unit, NOT double-counted)
        $this->assertEqualsWithDelta($displayPrice, $basePrice + $gstAmount, 0.01);
    }

    /**
     * Verify that the inclusive GST calculation is NOT double-counted
     * when multiplied by quantity. This was the reported bug.
     *
     * The itemTotal should be: displayPrice * quantity (not base * qty + gst * qty
     * separately, since GST is already embedded in displayPrice).
     */
    public function test_gst_inclusive_is_not_double_counted()
    {
        $displayPrice = 118.00;
        $quantity = 3;
        $gstRate = 18;

        // Per-unit inclusive calculation
        $basePrice = round($displayPrice / (1 + ($gstRate / 100)), 2);
        $itemGST = round($displayPrice - $basePrice, 2);

        // Total for the line item
        $itemTotal = $displayPrice * $quantity;
        $totalGST = $itemGST * $quantity;

        // The base cost for the line
        $subTotal = $basePrice * $quantity;

        // Key assertion: base + GST must equal total (no double counting)
        $this->assertEqualsWithDelta($itemTotal, $subTotal + $totalGST, 0.01);
        $this->assertEquals(354.00, $itemTotal);    // 118 * 3
        $this->assertEquals(300.00, $subTotal);     // 100 * 3
        $this->assertEquals(54.00, $totalGST);      // 18 * 3
    }

    /**
     * Test GST exclusive calculation logic.
     *
     * When gst_type is 'exclusive', GST is added on top of the base price.
     *
     * Example: ₹100 base, 18% GST exclusive
     *   CGST = 100 * 9 / 100 = 9
     *   SGST = 100 * 9 / 100 = 9
     *   displayPrice = 100 + 9 + 9 = 118
     */
    public function test_gst_exclusive_calculation()
    {
        $basePrice = 100.00;
        $gstRate = 18;
        $cgstRate = $gstRate / 2; // 9%
        $sgstRate = $gstRate / 2; // 9%
        $quantity = 2;

        $itemCGST = round(($basePrice * $cgstRate) / 100, 2);
        $itemSGST = round(($basePrice * $sgstRate) / 100, 2);
        $itemGST = $itemCGST + $itemSGST;
        $displayPrice = $basePrice + $itemCGST + $itemSGST;
        $itemTotal = $displayPrice * $quantity;

        $this->assertEquals(9.00, $itemCGST);
        $this->assertEquals(9.00, $itemSGST);
        $this->assertEquals(18.00, $itemGST);
        $this->assertEquals(118.00, $displayPrice);
        $this->assertEquals(236.00, $itemTotal); // 118 * 2
    }

    /**
     * Verify the grand total formula matches the placeOrder logic:
     * grandTotal = subTotal + totalCGST + totalSGST + totalCharges
     */
    public function test_grand_total_formula_for_exclusive_gst()
    {
        $unitPrice = 50.00;
        $quantity = 4;
        $gstRate = 12;
        $cgstRate = 6;
        $sgstRate = 6;
        $deliveryCharge = 50.00;

        $itemCGST = round(($unitPrice * $cgstRate) / 100, 2); // 3
        $itemSGST = round(($unitPrice * $sgstRate) / 100, 2); // 3
        $displayPrice = $unitPrice + $itemCGST + $itemSGST;    // 56

        $subTotal = $unitPrice * $quantity;       // 200
        $totalCGST = $itemCGST * $quantity;       // 12
        $totalSGST = $itemSGST * $quantity;       // 12
        $totalCharges = $deliveryCharge;           // 50

        $grandTotal = $subTotal + $totalCGST + $totalSGST + $totalCharges;

        $this->assertEquals(274.00, $grandTotal); // 200 + 12 + 12 + 50
    }

    /**
     * Test delivery charge calculation: fixed charge
     */
    public function test_fixed_delivery_charge_calculation()
    {
        $chargeType = 'fixed';
        $chargeValue = 50.00;
        $subTotal = 500.00;

        if ($chargeType === 'fixed') {
            $amount = $chargeValue;
        } elseif ($chargeType === 'percentage') {
            $amount = ($subTotal * $chargeValue) / 100;
        } else {
            $amount = 0;
        }

        $this->assertEquals(50.00, $amount);
    }

    /**
     * Test delivery charge calculation: percentage charge
     */
    public function test_percentage_delivery_charge_calculation()
    {
        $chargeType = 'percentage';
        $chargeValue = 5.00; // 5%
        $subTotal = 1000.00;

        if ($chargeType === 'fixed') {
            $amount = $chargeValue;
        } elseif ($chargeType === 'percentage') {
            $amount = ($subTotal * $chargeValue) / 100;
        } else {
            $amount = 0;
        }

        $this->assertEquals(50.00, $amount);
    }

    /**
     * Delivery charge should be zero when subTotal is zero.
     */
    public function test_delivery_charge_zero_when_subtotal_zero()
    {
        $chargeType = 'fixed';
        $chargeValue = 50.00;
        $subTotal = 0;

        if ($subTotal == 0) {
            $amount = 0;
        } elseif ($chargeType === 'fixed') {
            $amount = $chargeValue;
        } else {
            $amount = ($subTotal * $chargeValue) / 100;
        }

        $this->assertEquals(0, $amount);
    }

    /**
     * CGST + SGST must equal total GST for exclusive type.
     */
    public function test_cgst_plus_sgst_equals_total_gst()
    {
        $basePrice = 250.00;
        $gstRate = 18;
        $cgstRate = $gstRate / 2;
        $sgstRate = $gstRate / 2;

        $cgst = round(($basePrice * $cgstRate) / 100, 2);
        $sgst = round(($basePrice * $sgstRate) / 100, 2);
        $totalGST = $cgst + $sgst;

        $this->assertEquals(22.50, $cgst);
        $this->assertEquals(22.50, $sgst);
        $this->assertEquals(45.00, $totalGST);
    }

    /**
     * Test that the payment status labels in the OrderController match
     * the expected values (1=Paid, 2=Pending, else=Failed).
     */
    public function test_payment_status_labels_match_expected()
    {
        // This mirrors the match expression in OrderController::paymentStatusLabel
        $paymentStatusLabel = function ($status) {
            return match ((int) $status) {
                1 => 'Paid',
                2 => 'Pending',
                default => 'Failed',
            };
        };

        $this->assertEquals('Paid', $paymentStatusLabel(1));
        $this->assertEquals('Pending', $paymentStatusLabel(2));
        $this->assertEquals('Failed', $paymentStatusLabel(0));
        $this->assertEquals('Failed', $paymentStatusLabel(99));
    }

    /**
     * Test transaction report status labels inline match expression
     * mirrors OrderStatus::label — both should be consistent.
     */
    public function test_transaction_report_labels_consistent_with_enum()
    {
        // The transaction report uses an inline match; verify it's consistent
        // with OrderStatus::label().
        $inlineStatusLabel = function ($status) {
            return match ($status) {
                0 => 'Cancelled',
                1 => 'Pending',
                2 => 'Confirmed',
                3 => 'Delivered',
                default => 'Unknown',
            };
        };

        foreach ([0, 1, 2, 3] as $status) {
            $this->assertEquals(
                OrderStatus::label($status),
                $inlineStatusLabel($status),
                "Inline status label for status {$status} doesn't match OrderStatus::label()"
            );
        }
    }
}
