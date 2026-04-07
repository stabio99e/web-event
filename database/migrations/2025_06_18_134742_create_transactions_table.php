<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique();
            $table->string('merchant_ref');
            $table->string('payment_method');
            $table->string('payment_name');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('pay_code')->nullable();
            $table->string('checkout_url')->nullable();
            $table->string('status')->default('UNPAID'); //PAID, FAILED, EXPIRED, REFUND
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('fee_merchant')->nullable();
            $table->unsignedBigInteger('fee_customer')->nullable();
            $table->unsignedBigInteger('amount_received')->nullable();
            $table->timestamp('expired_time')->nullable();
            $table->json('raw_response');
            $table->timestamps();
        });    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
