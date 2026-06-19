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
        Schema::create('withdrawal_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->json('title');
            $table->enum('type', ['manual']);
            $table->enum('currency', ['IRR']);
            $table->integer('transaction_fee_percentage')->default(0);
            $table->integer('min_amount')->nullable();
            $table->integer('max_amount')->nullable();
            $table->enum('status', ['active', 'inactive']);
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
        Schema::dropIfExists('withdrawal_gateways');
    }
};
