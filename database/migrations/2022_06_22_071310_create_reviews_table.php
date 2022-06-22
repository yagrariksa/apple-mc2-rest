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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->char('uid')->unique();
            $table->char('name');
            $table->char('location');
            $table->char('district');
            $table->timestamps();
        });

        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->char('uid')->unique();
            $table->unsignedBigInteger('restaurant_id');
            $table->char('name');
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->char('uid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('food_id');
            $table->longText('desc');
            $table->integer('rating');
            $table->integer('price');
            $table->char('FDA');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('food_id')->references('id')->on('foods')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('foods');
        Schema::dropIfExists('restaurants');
    }
};
