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
        Schema::create('tbl_programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name');
            $table->date('program_date');
            $table->time('program_time');
            $table->string('program_place');
            $table->string('program_poster_url');
            $table->enum('program_type', ['Live', 'Recorded']);
            $table->string('program_link');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('tbl_programs');
    }
};
