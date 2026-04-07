<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('TotalPayAmount')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('admin_fee', 10, 2)->default(0);
            $table->decimal('ppn_fee', 5, 2)->default(0.00);
            $table->string('status')->default('UNPAID'); //PAID, FAILED, EXPIRED, REFUND
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
