<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds GST columns to tbl_product_stock_inventory that exist in
 * production but are missing from the original migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_product_stock_inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_product_stock_inventory', 'gst_rate')) {
                $table->decimal('gst_rate', 5, 2)->default(0)->after('discounted_unit_price');
            }
            if (!Schema::hasColumn('tbl_product_stock_inventory', 'gst_type')) {
                $table->string('gst_type')->default('inclusive')->after('gst_rate');
            }
            if (!Schema::hasColumn('tbl_product_stock_inventory', 'cgst_rate')) {
                $table->decimal('cgst_rate', 5, 2)->default(0)->after('gst_type');
            }
            if (!Schema::hasColumn('tbl_product_stock_inventory', 'sgst_rate')) {
                $table->decimal('sgst_rate', 5, 2)->default(0)->after('cgst_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_product_stock_inventory', function (Blueprint $table) {
            $table->dropColumn(['gst_rate', 'gst_type', 'cgst_rate', 'sgst_rate']);
        });
    }
};
