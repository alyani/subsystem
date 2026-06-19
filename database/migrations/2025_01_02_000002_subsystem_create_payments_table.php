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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->bigInteger('base_amount');
            $table->integer('transaction_fee_amount')->default(0);
            $table->bigInteger('amount');
            $table->enum('currency', ['IRR']);
            $table->enum('status', ['pending', 'processing', 'verified', 'failed'])->default('pending')->index();
            $table->string('gateway_reference', 100)->nullable()->unique();
            $table->string('iban', 20)->nullable()->index();
            $table->string('card_number', 20)->nullable()->index();
            $table->nullableMorphs('invoiceable', 'invoiceable');
            $table->enum('invoice_status', ['pending', 'processing', 'paid_uncompleted', 'completed', 'failed'])->nullable();
            $table->unsignedBigInteger('payment_gateway_id')->index();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('ip', 16)->nullable();
            $table->json('gateway_data')->nullable();
            $table->json('extra_data')->nullable();
            $table->text('error_data')->nullable();
            $table->timestamp('payment_date')->nullable();
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
        Schema::dropIfExists('payments');
    }
};
