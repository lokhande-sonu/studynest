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
        Schema::create('tbl_blogs', function (Blueprint $col) {
            $col->id('b_id');
            $col->string('b_title');
            $col->string('b_slug')->unique();
            $col->longText('b_content');
            $col->string('b_image')->nullable();
            $col->tinyInteger('b_status')->default(1); // 1: Active, 0: Inactive
            $col->string('b_meta_title')->nullable();
            $col->text('b_meta_description')->nullable();
            $col->string('b_meta_keywords')->nullable();
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_blogs');
    }
};
