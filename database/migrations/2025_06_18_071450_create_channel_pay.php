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
        Schema::create('channel_pay', function (Blueprint $table) {
            $table->id();
            $table->string('channel_name');
            $table->string('channel_code');
            $table->enum('channel_group', ['VA', 'Ewallet', 'Qris']);
            $table->enum('type', ['DIRECT', 'REDIRECT']);
            $table->integer('biaya_flat')->default(0);
            $table->decimal('biaya_percent', 5, 2)->default(0.0);
            $table->decimal('ppn', 5, 2)->default(0.0);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_pay');
    }
};
