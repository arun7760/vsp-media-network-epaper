<?php

namespace App\Console\Commands;

use App\Models\EpaperEdition;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledEditionsCommand extends Command
{
    protected $signature = 'epaper:publish-scheduled {--dry-run : Simulate publication without updating records}';

    protected $description = 'Publish approved E-Paper editions that have reached their scheduled publication time';

    public function handle(): int
    {
        $now = Carbon::now('Asia/Kolkata');
        $this->info("Checking scheduled E-Paper editions at {$now->toDateTimeString()} (Asia/Kolkata)...");

        // Find editions: status is 'scheduled', scheduled_at <= now, approved, and has pages
        $editions = EpaperEdition::with(['region', 'pages'])
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        if ($editions->isEmpty()) {
            $this->info('No pending scheduled editions found for release.');

            return 0;
        }

        $publishedCount = 0;
        $skippedCount = 0;

        foreach ($editions as $edition) {
            // Safety Check 1: Must have approved_by set
            if (! $edition->approved_by) {
                $this->warn("Skipping Edition ID {$edition->id} ({$edition->title}): Lacks editorial approval.");
                Log::warning("E-Paper Publish: Edition ID {$edition->id} skipped because approved_by is missing.");
                $skippedCount++;

                continue;
            }

            // Safety Check 2: Must have at least 1 page successfully processed
            if ($edition->pages->isEmpty() || $edition->total_pages === 0) {
                $this->warn("Skipping Edition ID {$edition->id} ({$edition->title}): No processed broadsheet pages found.");
                Log::warning("E-Paper Publish: Edition ID {$edition->id} skipped because no pages are processed.");
                $skippedCount++;

                continue;
            }

            // Safety Check 3: Ensure no pages in failed status
            $hasFailedPages = $edition->pages->contains(fn ($p) => $p->status === 'failed');
            if ($hasFailedPages) {
                $this->error("Skipping Edition ID {$edition->id} ({$edition->title}): Contains failed page conversions.");
                Log::error("E-Paper Publish: Edition ID {$edition->id} skipped because pages failed conversion.");
                $skippedCount++;

                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("[Dry Run] Would publish Edition ID {$edition->id}: {$edition->title}");
                $publishedCount++;

                continue;
            }

            // Execute Publication Atomically
            $fromStatus = $edition->status;
            $edition->update([
                'status' => 'published',
                'published_at' => $now,
            ]);

            // Record in audit workflow logs
            $edition->recordWorkflow(
                action: 'auto_publish',
                fromStatus: $fromStatus,
                toStatus: 'published',
                notes: 'Automated 5:00 AM IST release via Laravel Scheduler'
            );

            Log::info("E-Paper Publish: Edition ID {$edition->id} ({$edition->title}) published live successfully.");
            $this->info("✓ Published Edition ID {$edition->id}: {$edition->title}");
            $publishedCount++;
        }

        $this->info("Execution complete: {$publishedCount} published, {$skippedCount} skipped.");

        return 0;
    }
}
