<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Regional Editions (Hyderabad, Visakhapatnam, Amaravati/Vijayawada, Main)
        Schema::create('epaper_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. ప్రధాన సంచిక / విశాఖపట్నం ఎడిషన్
            $table->string('name_en')->nullable(); // Main Edition / Visakhapatnam Edition
            $table->string('slug')->unique(); // main, visakhapatnam, amaravati, hyderabad
            $table->string('code', 10)->nullable(); // MAIN, VSKP, AMR, HYD
            $table->integer('display_order')->default(1);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 2. Daily Broadsheet Editions
        Schema::create('epaper_editions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('epaper_regions')->cascadeOnDelete();
            $table->date('edition_date')->index();
            $table->string('edition_code')->nullable()->index(); // e.g. 2026-10-23-main
            $table->string('title'); // e.g. విఎస్పీ టైమ్స్ ప్రధాన సంచిక
            $table->string('title_en')->nullable();
            $table->string('volume_number')->nullable(); // సంపుటి 1
            $table->string('issue_number')->nullable(); // సంచిక 108
            $table->unsignedInteger('total_pages')->default(0);

            // Storage References (Full Edition PDF & Cover Previews)
            $table->string('master_pdf_path')->nullable();
            $table->unsignedBigInteger('master_pdf_size')->nullable(); // bytes
            $table->string('cover_image_path')->nullable();
            $table->string('cover_thumbnail_path')->nullable();

            // Status & Scheduled Publication
            $table->enum('status', [
                'draft',
                'pending_approval',
                'scheduled',
                'published',
                'archived',
            ])->default('draft')->index();

            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable();

            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('downloads_count')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['region_id', 'edition_date']);
        });

        // 3. High-Resolution Decomposed Pages
        Schema::create('epaper_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained('epaper_editions')->cascadeOnDelete();
            $table->unsignedInteger('page_number');
            $table->string('page_title')->nullable(); // e.g. ప్రధాన వార్తలు, జిల్లా పేజీ, క్రీడలు, సినిమా
            $table->string('page_title_en')->nullable();

            $table->string('image_path')->nullable(); // High-res broadsheet WebP / JPEG
            $table->string('thumbnail_path')->nullable(); // Bottom filmstrip thumb
            $table->string('page_pdf_path')->nullable(); // Single-page PDF if available

            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->enum('status', [
                'pending_processing',
                'processing',
                'ready',
                'failed',
            ])->default('ready')->index();

            $table->text('error_message')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['edition_id', 'page_number']);
        });

        // 4. Interactive Article Clips / Hot Zones (Clipping & Reading Mode)
        Schema::create('epaper_clips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('epaper_pages')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title')->nullable();
            $table->decimal('box_x', 8, 2);
            $table->decimal('box_y', 8, 2);
            $table->decimal('box_width', 8, 2);
            $table->decimal('box_height', 8, 2);
            $table->string('clip_image_path')->nullable();
            $table->longText('article_text')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });

        // 5. Workflow & Publication Audit Logs
        Schema::create('epaper_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained('epaper_editions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // uploaded, submitted, approved, scheduled, published
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epaper_workflow_logs');
        Schema::dropIfExists('epaper_clips');
        Schema::dropIfExists('epaper_pages');
        Schema::dropIfExists('epaper_editions');
        Schema::dropIfExists('epaper_regions');
    }
};
