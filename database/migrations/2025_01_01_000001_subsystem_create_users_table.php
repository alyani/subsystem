<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('family')->nullable();
            $table->string('nickname')->nullable();
            $table->unsignedSmallInteger('country_code')->nullable();
            $table->string('mobile', 20)->nullable()->unique();
            $table->enum('gender', ['none', 'male', 'female'])->default('none');
            $table->bigInteger('balance')->default(0);
            $table->enum('currency', ['IRR'])->default('IRR');
            $table->bigInteger('token_balance')->default(0);
            $table->enum('status', [
                'waitingForSetProfile',
                'active',
                'banned',
            ])->default('waitingForSetProfile')->index();
            $table->string('email')->nullable()->unique();
            $table->string('national_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('company_name')->nullable();
            $table->bigInteger('birth_date')->nullable();
            $table->string('avatarSID')->nullable();
            $table->unsignedBigInteger('referee_user_id')->nullable();
            $table->string('referral_code')->nullable();
            $table->integer('referred_users_count')->default(0);
            $table->integer('score')->default(0);
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_activity')->nullable();
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
        Schema::drop('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
};
