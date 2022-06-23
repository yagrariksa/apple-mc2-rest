<?php

use App\Models\User;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('role')->nullable();
        });

        $r = ['anak kos', 'anak mentri', 'anak DPR', 'anak hits'];
        foreach (User::get() as $user) {
            $user->image = 'https://api.lorem.space/image/face?w=150&h=150';
            $user->role = $r[array_rand($r)];
            $user->save();
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
            $table->string('role')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
