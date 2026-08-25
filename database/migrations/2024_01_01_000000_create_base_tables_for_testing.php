<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the core application tables that were originally created
 * outside of Laravel's migration system.
 *
 * These tables already exist in production.  The `hasTable` guard
 * ensures this migration is a safe no-op when run via `artisan migrate`
 * in production.  During `artisan migrate:fresh` (testing) the tables
 * are dropped first, so this migration recreates them and lets the
 * later ALTER migrations succeed.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── tbl_customers (ALTER'd at 2026_01_25 & 2026_04_13) ──────────
        if (!Schema::hasTable('tbl_customers')) {
            Schema::create('tbl_customers', function (Blueprint $t) {
                $t->bigIncrements('cust_id');
                $t->string('cust_name');
                $t->string('cust_email');
                $t->string('cust_mobile')->nullable();
                $t->string('cust_address')->nullable();
                $t->string('cust_city')->nullable();
                $t->string('cust_state')->nullable();
                $t->string('cust_country')->nullable();
                $t->string('cust_pincode')->nullable();
                $t->string('cust_gender')->nullable();
                $t->string('cust_profile_photo')->nullable();
                $t->string('cust_api_token')->nullable();
                $t->timestamp('cust_api_token_validity')->nullable();
                $t->tinyInteger('cust_status')->default(1);
                $t->timestamp('cust_created_at')->nullable();
                $t->timestamp('cust_updated_at')->nullable();
            });
        }

        // ── tbl_products (ALTER'd at 2026_01_24) ─────────────────────────
        if (!Schema::hasTable('tbl_products')) {
            Schema::create('tbl_products', function (Blueprint $t) {
                $t->bigIncrements('p_id');
                $t->unsignedBigInteger('p_cat_id')->nullable();
                $t->string('p_name');
                $t->string('p_tag')->nullable();
                $t->text('p_highlight')->nullable();
                $t->text('p_short_desc')->nullable();
                $t->text('p_full_desc')->nullable();
                $t->string('p_photo')->nullable();
                $t->tinyInteger('p_status')->default(1);
                $t->timestamp('p_published_on')->nullable();
                $t->timestamp('p_created_at')->nullable();
                $t->timestamp('p_updated_at')->nullable();
            });
        }

        // ── tbl_orders (ALTER'd at 2026_04_13) ───────────────────────────
        if (!Schema::hasTable('tbl_orders')) {
            Schema::create('tbl_orders', function (Blueprint $t) {
                $t->bigIncrements('order_id');
                $t->unsignedBigInteger('order_placed_cust_id');
                $t->integer('order_items_qty')->default(0);
                $t->text('order_items')->nullable();
                $t->text('order_delivery_details')->nullable();
                $t->text('order_charges')->nullable();
                $t->decimal('order_total_amt', 10, 2)->default(0);
                $t->decimal('order_paid_amt', 10, 2)->default(0);
                $t->decimal('order_due_amt', 10, 2)->default(0);
                $t->string('order_payment_id')->nullable();
                $t->tinyInteger('order_payment_mode')->default(1);
                $t->timestamp('order_date_time')->nullable();
                $t->timestamp('order_payment_date_time')->nullable();
                $t->text('order_tracking_details')->nullable();
                $t->tinyInteger('order_payment_status')->default(2);
                $t->tinyInteger('order_status')->default(1);
                $t->timestamp('order_created_at')->nullable();
                $t->timestamp('order_updated_at')->nullable();
            });
        }

        // ── tbl_bundle_products (ALTER'd at 2026_04_13) ──────────────────
        if (!Schema::hasTable('tbl_bundle_products')) {
            Schema::create('tbl_bundle_products', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('bundle_id');
                $t->unsignedBigInteger('product_id');
            });
        }

        // ── tbl_product_categories ───────────────────────────────────────
        if (!Schema::hasTable('tbl_product_categories')) {
            Schema::create('tbl_product_categories', function (Blueprint $t) {
                $t->bigIncrements('cat_id');
                $t->string('cat_name');
                $t->string('cat_photo')->nullable();
                $t->tinyInteger('cat_status')->default(1);
                $t->timestamp('cat_created_at')->nullable();
                $t->timestamp('cat_updated_at')->nullable();
            });
        }

        // ── tbl_charges ──────────────────────────────────────────────────
        if (!Schema::hasTable('tbl_charges')) {
            Schema::create('tbl_charges', function (Blueprint $t) {
                $t->bigIncrements('charge_id');
                $t->string('charge_name');
                $t->string('charge_type'); // fixed | percentage
                $t->decimal('charge_value', 10, 2)->default(0);
                $t->tinyInteger('charge_status')->default(1);
                $t->timestamp('charge_created_at')->nullable();
                $t->timestamp('charge_updated_at')->nullable();
            });
        }

        // ── tbl_app_slider ───────────────────────────────────────────────
        if (!Schema::hasTable('tbl_app_slider')) {
            Schema::create('tbl_app_slider', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->string('slider_photo_url')->nullable();
                $t->string('caption')->nullable();
                $t->tinyInteger('status')->default(1);
                $t->timestamps();
            });
        }

        // ── tbl_bundles ──────────────────────────────────────────────────
        if (!Schema::hasTable('tbl_bundles')) {
            Schema::create('tbl_bundles', function (Blueprint $t) {
                $t->bigIncrements('b_id');
                $t->string('b_name');
                $t->text('b_short_desc')->nullable();
                $t->string('b_image')->nullable();
                $t->tinyInteger('b_status')->default(1);
                $t->boolean('is_all_schools')->default(0);
                $t->boolean('is_all_classes')->default(0);
                $t->boolean('is_all_subjects')->default(0);
                $t->timestamp('b_created_at')->nullable();
                $t->timestamp('b_updated_at')->nullable();
            });
        }

        // ── tbl_managements ──────────────────────────────────────────────
        if (!Schema::hasTable('tbl_managements')) {
            Schema::create('tbl_managements', function (Blueprint $t) {
                $t->bigIncrements('m_id');
                $t->string('name');
                $t->string('email');
                $t->string('username');
                $t->string('mobile')->nullable();
                $t->timestamp('email_verified_at')->nullable();
                $t->string('password');
                $t->string('role')->default('admin');
                $t->string('department')->nullable();
                $t->string('work_location')->nullable();
                $t->string('profile_photo')->nullable();
                $t->string('api_token')->nullable();
                $t->tinyInteger('status')->default(1);
                $t->timestamps();
            });
        }

        // ── tbl_bundle_schools ───────────────────────────────────────────
        if (!Schema::hasTable('tbl_bundle_schools')) {
            Schema::create('tbl_bundle_schools', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('bundle_id');
                $t->unsignedBigInteger('school_id');
            });
        }

        // ── tbl_bundle_classes ───────────────────────────────────────────
        if (!Schema::hasTable('tbl_bundle_classes')) {
            Schema::create('tbl_bundle_classes', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('bundle_id');
                $t->unsignedBigInteger('class_id');
            });
        }

        // ── tbl_bundle_subjects ──────────────────────────────────────────
        if (!Schema::hasTable('tbl_bundle_subjects')) {
            Schema::create('tbl_bundle_subjects', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('bundle_id');
                $t->unsignedBigInteger('subject_id');
            });
        }

        // ── tbl_prod_inventory (legacy) ──────────────────────────────────
        if (!Schema::hasTable('tbl_prod_inventory')) {
            Schema::create('tbl_prod_inventory', function (Blueprint $t) {
                $t->bigIncrements('p_stock_id');
                $t->unsignedBigInteger('product_id');
                $t->string('packet_size')->nullable();
                $t->string('product_unit')->nullable();
                $t->decimal('product_rate', 10, 2)->default(0);
                $t->integer('p_stock_qty')->default(0);
                $t->tinyInteger('p_stock_status')->default(1);
                $t->timestamp('p_stock_created_at')->nullable();
                $t->timestamp('p_stock_updated_at')->nullable();
            });
        }

        // ── password_resets ──────────────────────────────────────────────
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $t) {
                $t->string('email')->index();
                $t->string('token');
                $t->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // This migration is designed to be safe in production.
        // Dropping tables here would be dangerous, so we leave the down empty.
    }
};
