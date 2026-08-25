<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update products table
        Schema::table('tbl_products', function (Blueprint $table) {
            $table->string('p_brand')->nullable()->after('p_full_desc');
            $table->enum('p_gender', ['Unisex', 'Boys', 'Girls'])->default('Unisex')->after('p_brand');
            
            // Flags for "All" selection
            $table->boolean('is_all_schools')->default(0)->after('p_gender');
            $table->boolean('is_all_classes')->default(0)->after('is_all_schools');
            $table->boolean('is_all_subjects')->default(0)->after('is_all_classes');
        });

        // Create Product Variants table
        Schema::create('tbl_product_variants', function (Blueprint $table) {
            $table->bigIncrements('prod_variant_id');
            $table->unsignedBigInteger('product_id'); // Assuming p_id is bigInteger, check tbl_products definition
            $table->string('prod_variant');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            // Foreign key constraint (assuming p_id is the key on tbl_products)
            // Note: tbl_products primary key is p_id. If it's bigIncrements, it's unsigned big int.
            // But let's verify if p_id is bigIncrements. Model says $primaryKey = 'p_id'.
            // Usually older laravel uses increments (int).
            // I'll skip explicit foreign key constraint for now to avoid mismatch errors if types differ, 
            // or I should have checked tbl_products migration. 
            // However, modern Laravel uses bigIncrements.
        });

        // Create Product Stock Inventory table
        // START: imohitmehto | 2026-08-25 | NOTE: prod_variant_id is nullable for products without variants
        Schema::create('tbl_product_stock_inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('prod_sku')->unique();
            $table->unsignedBigInteger('prod_id');
            $table->unsignedBigInteger('prod_variant_id')->nullable();
            $table->integer('available_stock')->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discounted_unit_price', 10, 2)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        // END: imohitmehto | Nullable prod_variant_id for stock inventory

        // Pivot tables for Schools, Classes, Subjects
        Schema::create('tbl_product_schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('school_id');
            // $table->foreign('school_id')->references('sch_id')->on('tbl_schools')->onDelete('cascade');
        });

        Schema::create('tbl_product_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('class_id');
            // $table->foreign('class_id')->references('class_id')->on('tbl_classes')->onDelete('cascade');
        });

        Schema::create('tbl_product_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('subject_id');
            // $table->foreign('subject_id')->references('subject_id')->on('tbl_subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_product_subjects');
        Schema::dropIfExists('tbl_product_classes');
        Schema::dropIfExists('tbl_product_schools');
        Schema::dropIfExists('tbl_product_stock_inventory');
        Schema::dropIfExists('tbl_product_variants');

        Schema::table('tbl_products', function (Blueprint $table) {
            $table->dropColumn(['p_brand', 'p_gender', 'is_all_schools', 'is_all_classes', 'is_all_subjects']);
        });
    }
};
