<?php
/**
 * START: imohitmehto | 2026-08-25 | FIX: Make prod_variant_id nullable on tbl_product_stock_inventory
 * Products without variants (e.g., single-variant items) need a NULL prod_variant_id
 * in stock inventory. The original migration had this as NOT NULL which caused
 * SQLite test failures and would fail on MySQL for non-variant products.
 * This migration is MySQL-specific with a SQLite guard.
 * END: imohitmehto | FIX: Nullable prod_variant_id
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * MySQL/MariaDB only — SQLite already has this as nullable from the
     * original migration. Raw statement used because doctrine/dbal is
     * not installed.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE tbl_product_stock_inventory MODIFY COLUMN prod_variant_id BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE tbl_product_stock_inventory MODIFY COLUMN prod_variant_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
