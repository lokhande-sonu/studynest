<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cust_id')->nullable();
            $table->string('email');
            $table->string('ip_address');
            $table->boolean('successful')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_login_attempts');
    }
};