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
        Schema::create('tbl_cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cust_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamps();

            // We assume tbl_customers and tbl_products exist. 
            // Note: tbl_customers uses cust_id, tbl_products uses p_id.
            // I'll skip foreign key constraints for now to avoid issues if types mismatch, 
            // but in a real app I should add them. The existing codebase seems inconsistent with FKs.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_cart_items');
    }
};
