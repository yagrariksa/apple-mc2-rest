<?php

use App\Models\Review;
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
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('porsi')->nullable();
        });

        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        foreach (Review::get() as $review) {
            $review->porsi = $p[array_rand($p)];
            $review->save();
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('porsi')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('porsi');
        });
    }
};
