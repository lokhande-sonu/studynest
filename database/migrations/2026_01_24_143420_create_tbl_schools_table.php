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
        Schema::create('tbl_schools', function (Blueprint $table) {
            $table->bigIncrements('sch_id');
            $table->string('sch_name');
            $table->string('sch_mobile');
            $table->string('sch_email');
            $table->text('sch_address');
            $table->string('sch_city');
            $table->string('sch_logo')->nullable();
            $table->integer('sch_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_schools');
    }
};
