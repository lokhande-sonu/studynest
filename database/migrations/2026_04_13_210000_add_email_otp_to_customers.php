<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_customers', function (Blueprint $table) {
            $table->string('email_otp')->nullable()->after('cust_email');
            $table->timestamp('email_otp_expires_at')->nullable()->after('email_otp');
            $table->boolean('email_verified')->default(false)->after('email_otp_expires_at');
        });
    }

    public function down()
    {
        Schema::table('tbl_customers', function (Blueprint $table) {
            $table->dropColumn(['email_otp', 'email_otp_expires_at', 'email_verified']);
        });
    }
};
