<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile')->unique();
            $table->string('email');
            $table->string('password');
            $table->enum('account_status',['pending', 'active', 'blocked', 'deleted', 'suspended'])->default('pending');
            $table->timestamp('mobile_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        DB::update("ALTER TABLE users AUTO_INCREMENT = 13031;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
