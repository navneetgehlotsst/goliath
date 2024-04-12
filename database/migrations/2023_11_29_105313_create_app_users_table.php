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
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('full_name');
            $table->string('slug');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->integer('country_code')->default(61);
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->enum('role',['admin','user'])->default('user');
            $table->enum('type',['email','mobile'])->default('mobile');
            $table->string('otp')->nullable();
            $table->string('otp_expired')->nullable();
            $table->text('avatar')->default('');
            $table->string('device_token')->default('');
            $table->enum('device_type',['android','ios'])->default('ios');
            $table->enum('status',['active','inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_users');
    }
};
