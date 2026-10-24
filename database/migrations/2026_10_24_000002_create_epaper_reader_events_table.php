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
        Schema::create('epaper_reader_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->nullable()->constrained('epaper_editions')->nullOnDelete();
            $table->string('visitor_token', 64)->index();
            $table->string('event_type', 32)->index(); // edition_open, page_view, page_nav, pdf_download
            $table->unsignedSmallInteger('page_number')->nullable();
            $table->date('event_date')->index(); // Stored in Asia/Kolkata
            $table->string('device_type', 16)->default('desktop'); // mobile, desktop, tablet
            $table->string('traffic_source', 32)->default('direct'); // direct, google_search, social, referral, other
            $table->string('referrer_host', 255)->nullable();
            $table->timestamps();

            // Composite indexes for rapid analytics queries
            $table->index(['edition_id', 'event_date', 'event_type']);
            $table->index(['event_date', 'event_type']);
            $table->index(['visitor_token', 'edition_id', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epaper_reader_events');
    }
};
