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
        Schema::create('web_config', function (Blueprint $table) {
            $table->id();

            // Pengaturan umum
            $table->string('site_name')->nullable();
            $table->string('site_tagline')->nullable();
            $table->text('site_description')->nullable();

            // Kontak umum
            $table->string('contact_email')->nullable();
            $table->string('contact_whatsapp')->nullable(); 

            // Logo & Favicon
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();

            // Meta SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_config');
    }
};
