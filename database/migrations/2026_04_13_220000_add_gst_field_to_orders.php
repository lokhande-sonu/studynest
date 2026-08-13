<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->string('order_gst_number')->nullable()->after('order_delivery_details');
            $table->decimal('order_gst_amount', 10, 2)->default(0)->after('order_gst_number');
        });
    }

    public function down()
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->dropColumn(['order_gst_number', 'order_gst_amount']);
        });
    }
};
