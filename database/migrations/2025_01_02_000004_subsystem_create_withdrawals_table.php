<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->bigInteger('base_amount');
            $table->integer('transaction_fee_amount')->default(0);
            $table->bigInteger('amount');
            $table->enum('currency', ['IRR']);
            $table->enum('status', ['pending', 'processing', 'verified', 'failed'])->default('pending')->index();
            $table->unsignedBigInteger('withdrawal_gateway_id')->index();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('ip', 16)->nullable();
            $table->json('gateway_data')->nullable();
            $table->json('extra_data')->nullable();
            $table->text('error_data')->nullable();
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
        Schema::dropIfExists('withdrawals');
    }
};
