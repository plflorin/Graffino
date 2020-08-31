<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('instagram_id',50);
            $table->string('low_resolution_url',250);
            $table->string('low_resolution_width',100);
            $table->string('low_resolution_height',100);
            $table->string('thumbnail_url',250);
            $table->string('thumbnail_width',100);
            $table->string('thumbnail_height',100);
            $table->string('standard_resolution_url',250);
            $table->string('standard_resolution_width',100);
            $table->string('standard_resolution_height',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
