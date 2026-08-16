<?php

namespace Tests\Unit;

use App\Models\Charge;
use App\Services\PricingService;
use PHPUnit\Framework\TestCase;

class PricingServiceTest extends TestCase
{
    public function testInclusiveItemPricing_rounds_and_keeps_total_consistent(): void
    {
        $p = PricingService::itemPricing(100, 18, 'inclusive', 2);

        $this->assertSame(84.75, $p['base_unit_price']);
        $this->assertSame(15.25, $p['gst_per_unit']);
        $this->assertSame(7.63, $p['cgst_per_unit']);
        $this->assertSame(7.62, $p['sgst_per_unit']);
        $this->assertSame(100.0, $p['unit_price_incl_gst']);
        $this->assertSame(169.50, $p['line_base']);
        $this->assertSame(15.26, $p['line_cgst']);
        $this->assertSame(15.24, $p['line_sgst']);
        $this->assertSame(30.50, $p['line_gst']);
        $this->assertSame(200.00, $p['line_total_incl_gst']);

        // base + tax must reconstruct the inclusive total exactly
        $this->assertSame(
            $p['line_total_incl_gst'],
            round($p['line_base'] + $p['line_cgst'] + $p['line_sgst'], 2)
        );
    }

    public function testInclusiveItemPricing_qty3_rounding_stays_consistent(): void
    {
        $p = PricingService::itemPricing(10, 18, 'inclusive', 3);

        $this->assertSame(8.47, $p['base_unit_price']);
        $this->assertSame(1.53, $p['gst_per_unit']);
        $this->assertSame(30.00, $p['line_total_incl_gst']);
        $this->assertSame(25.41, $p['line_base']);
        $this->assertSame(4.59, $p['line_gst']);
        $this->assertSame(
            $p['line_total_incl_gst'],
            round($p['line_base'] + $p['line_gst'], 2)
        );
    }

    public function testExclusiveItemPricing_adds_gst_on_top(): void
    {
        $p = PricingService::itemPricing(500, 18, 'exclusive', 1);

        $this->assertSame(500.00, $p['base_unit_price']);
        $this->assertSame(45.00, $p['cgst_per_unit']);
        $this->assertSame(45.00, $p['sgst_per_unit']);
        $this->assertSame(90.00, $p['gst_per_unit']);
        $this->assertSame(590.00, $p['unit_price_incl_gst']);
        $this->assertSame(590.00, $p['line_total_incl_gst']);
    }

    public function testItemPricing_zero_gst(): void
    {
        $p = PricingService::itemPricing(100, 0, 'inclusive', 1);

        $this->assertSame(100.00, $p['base_unit_price']);
        $this->assertSame(0.0, $p['gst_per_unit']);
        $this->assertSame(100.00, $p['line_total_incl_gst']);
    }

    public function testChargePricing_exclusive_matches_historical_delivery(): void
    {
        $p = PricingService::chargePricing(10, 18, 'exclusive', 9, 9);

        $this->assertSame(10.00, $p['base_amount']);
        $this->assertSame(0.90, $p['cgst_amount']);
        $this->assertSame(0.90, $p['sgst_amount']);
        $this->assertSame(1.80, $p['gst_amount']);
        $this->assertSame(11.80, $p['total_amount_incl_gst']);
    }

    public function testChargePricing_inclusive_backs_out_gst(): void
    {
        $p = PricingService::chargePricing(11.80, 18, 'inclusive', 9, 9);

        $this->assertSame(11.80, $p['total_amount_incl_gst']);
        $this->assertSame(1.80, $p['gst_amount']);
        $this->assertSame(0.90, $p['cgst_amount']);
        $this->assertSame(0.90, $p['sgst_amount']);
    }

    public function testChargePricing_zero_base_is_zero(): void
    {
        $p = PricingService::chargePricing(0, 18, 'exclusive', 9, 9);

        $this->assertSame(0.00, $p['base_amount']);
        $this->assertSame(0.00, $p['gst_amount']);
        $this->assertSame(0.00, $p['total_amount_incl_gst']);
    }

    public function testIsChargeWaived_above_threshold(): void
    {
        $charge = new Charge();
        $charge->minimum_cart_value = 2000;

        $this->assertTrue(PricingService::isChargeWaived($charge, 2000));
        $this->assertTrue(PricingService::isChargeWaived($charge, 2500.50));
        $this->assertFalse(PricingService::isChargeWaived($charge, 1999.99));
    }

    public function testIsChargeWaived_disabled_when_threshold_zero(): void
    {
        $charge = new Charge();
        $charge->minimum_cart_value = 0;

        $this->assertFalse(PricingService::isChargeWaived($charge, 99999));
    }

    public function testChargeBaseAmount_fixed_and_percentage(): void
    {
        $fixed = new Charge();
        $fixed->charge_type = 'fixed';
        $fixed->charge_value = 10;

        $percent = new Charge();
        $percent->charge_type = 'percentage';
        $percent->charge_value = 5;

        $this->assertSame(10.0, PricingService::chargeBaseAmount($fixed, 1000, 1000));
        $this->assertSame(50.0, PricingService::chargeBaseAmount($percent, 1000, 1000));
    }

    public function testTotals_aggregates_lines_and_charges(): void
    {
        $lines = [
            PricingService::itemPricing(100, 18, 'inclusive', 2),
            PricingService::itemPricing(500, 18, 'exclusive', 1),
        ];
        $charges = [
            PricingService::chargePricing(10, 18, 'exclusive', 9, 9),
        ];

        $totals = PricingService::totals($lines, $charges);

        // 200.00 + 590.00 items, + 11.80 delivery
        $this->assertSame(790.00, $totals['subtotal_inclusive']);
        $this->assertSame(669.50, $totals['subtotal_base']);
        $this->assertSame(120.50, $totals['product_gst']);
        $this->assertSame(11.80, $totals['charges_total']);
        $this->assertSame(801.80, $totals['grand_total']);
        $this->assertSame(round(790.00 + 11.80, 2), $totals['grand_total']);
    }

    public function testTotals_no_charges(): void
    {
        $lines = [
            PricingService::itemPricing(100, 18, 'inclusive', 1),
        ];

        $totals = PricingService::totals($lines, []);

        $this->assertSame(100.00, $totals['subtotal_inclusive']);
        $this->assertSame(0.0, $totals['charges_total']);
        $this->assertSame(100.00, $totals['grand_total']);
    }
}
