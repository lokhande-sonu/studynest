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
        Schema::create('tbl_podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('podcast_title');
            $table->string('podcast_speaker');
            $table->string('podcast_duration');
            $table->string('podcast_thumbnail');
            $table->string('podcast_audio');
            $table->text('podcast_description')->nullable();
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
        Schema::dropIfExists('tbl_podcasts');
    }
};
