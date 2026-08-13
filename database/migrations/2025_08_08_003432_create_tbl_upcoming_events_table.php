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
        Schema::create('tbl_upcoming_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->dateTime('event_date_time');
            $table->string('event_place');
            $table->string('event_poster_url');
            $table->text('event_details')->nullable();
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
        Schema::dropIfExists('tbl_upcoming_events');
    }
};
