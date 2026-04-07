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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('order_item_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // jika tiket untuk diri sendiri
            $table->string('attendee_name'); // nama peserta
            $table->string('attendee_email')->nullable();
            $table->string('attendance_status', 20)->nullable()->comment('present, absent, pending')->default('pending');
            $table->text('attendance_note')->nullable();
            $table->timestamp('checkin_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
