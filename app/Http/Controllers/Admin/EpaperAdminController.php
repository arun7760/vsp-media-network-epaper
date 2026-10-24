<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EpaperEdition;
use App\Models\EpaperPage;
use App\Models\EpaperRegion;
use App\Models\EpaperWorkflowLog;
use App\Services\EpaperPdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EpaperAdminController extends Controller
{
    /**
     * Admin Overview Dashboard
     */
    public function dashboard()
    {
        $metrics = [
            'total_editions' => EpaperEdition::count(),
            'published_editions' => EpaperEdition::where('status', 'published')->count(),
            'pending_approvals' => EpaperEdition::where('status', 'pending_approval')->count(),
            'scheduled_editions' => EpaperEdition::where('status', 'scheduled')->count(),
            'total_views' => EpaperEdition::sum('views_count'),
            'total_downloads' => EpaperEdition::sum('downloads_count'),
        ];

        $pendingEditions = EpaperEdition::with(['region', 'creator'])
            ->where('status', 'pending_approval')
            ->latest()
            ->take(5)
            ->get();

        $recentEditions = EpaperEdition::with('region')
            ->latest('edition_date')
            ->take(8)
            ->get();

        $recentLogs = EpaperWorkflowLog::with(['edition', 'user'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('metrics', 'pendingEditions', 'recentEditions', 'recentLogs'));
    }

    /**
     * Manage Editions List with Search, Filter & Status Badges
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $regionId = $request->query('region_id');
        $date = $request->query('date');
        $search = trim($request->query('search', ''));
        $perPage = (int) $request->query('per_page', 50);

        $query = EpaperEdition::with(['region', 'creator', 'approver'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($regionId, fn ($q) => $q->where('region_id', $regionId))
            ->when($date, fn ($q) => $q->whereDate('edition_date', $date))
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('edition_code', 'like', "%{$search}%")
                        ->orWhere('volume_number', 'like', "%{$search}%")
                        ->orWhere('issue_number', 'like', "%{$search}%")
                        ->orWhereHas('region', function ($r) use ($search) {
                            $r->where('name', 'like', "%{$search}%")
                              ->orWhere('name_te', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('edition_date');

        $statusCounts = [
            'all' => EpaperEdition::count(),
            'pending_approval' => EpaperEdition::where('status', 'pending_approval')->count(),
            'scheduled' => EpaperEdition::where('status', 'scheduled')->count(),
            'published' => EpaperEdition::where('status', 'published')->count(),
            'draft' => EpaperEdition::where('status', 'draft')->count(),
        ];

        $editions = $query->paginate($perPage)->withQueryString();
        $regions = EpaperRegion::active()->get();

        return view('admin.editions.index', compact(
            'editions',
            'regions',
            'status',
            'regionId',
            'date',
            'search',
            'statusCounts'
        ));
    }

    /**
     * Upload New Edition Form
     */
    public function create()
    {
        $regions = EpaperRegion::active()->get();
        return view('admin.editions.create', compact('regions'));
    }

    /**
     * Secure PDF Upload & Processing
     */
    public function store(Request $request, EpaperPdfService $pdfService)
    {
        $validated = $request->validate([
            'region_id' => ['required', 'exists:epaper_regions,id'],
            'edition_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'volume_number' => ['nullable', 'string', 'max:50'],
            'issue_number' => ['nullable', 'string', 'max:50'],
            'publish_mode' => ['nullable', 'string', 'in:publish_now,schedule,draft'],
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:102400'], // 100MB max
        ]);

        $region = EpaperRegion::findOrFail($validated['region_id']);
        $date = Carbon::parse($validated['edition_date']);
        $dateString = $date->toDateString();
        $editionCode = $date->format('Y-m-d') . '-' . $region->slug;

        // Duplicate Check: Same region on same date (including soft-deleted / trashed records)
        $existing = EpaperEdition::withTrashed()
            ->where('region_id', $region->id)
            ->whereDate('edition_date', $dateString)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Previously deleted edition - purge it completely so the new upload replaces it seamlessly
                $existing->forceDelete();
            } else {
                return back()->withInput()->withErrors([
                    'edition_date' => 'ఎంచుకున్న ప్రాంతానికి ఈ తేదీన (' . $date->format('d-m-Y') . ') ఇప్పటికే ఒక సంచిక సృష్టించబడింది. దయచేసి ఉన్న సంచికను సవరించండి లేదా వేరొక తేదీని ఎంచుకోండి. (An edition already exists for this date and region).',
                ]);
            }
        }

        // Duplicate check for edition code as well
        $codeExisting = EpaperEdition::withTrashed()
            ->where('edition_code', $editionCode)
            ->first();

        if ($codeExisting) {
            if ($codeExisting->trashed()) {
                $codeExisting->forceDelete();
            } else {
                return back()->withInput()->withErrors([
                    'edition_date' => "ఈ సంచిక కోడ్‌తో ({$editionCode}) ఇప్పటికే ఒక సంచిక ఉంది.",
                ]);
            }
        }

        // Validate PDF Integrity & Magic Header
        $check = $pdfService->validatePdf($request->file('pdf_file'));
        if (! $check['valid']) {
            return back()->withInput()->withErrors(['pdf_file' => $check['error']]);
        }

        // Create Edition in Draft with defensive exception handling
        try {
            $edition = EpaperEdition::create([
                'region_id' => $region->id,
                'edition_date' => $dateString,
                'edition_code' => $editionCode,
                'title' => $validated['title'],
                'volume_number' => $validated['volume_number'] ?? 'సంపుటి 1',
                'issue_number' => $validated['issue_number'] ?? 'సంచిక 1',
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'edition_date' => 'ఎంచుకున్న ప్రాంతం మరియు తేదీకి ఇప్పటికే ఒక సంచిక ఉంది. దయచేసి ఉన్న సంచికను సవరించండి లేదా వేరొక తేదీని ఎంచుకోండి.',
            ]);
        }

        // Process PDF decomposition
        $success = $pdfService->processEditionPdf($request->file('pdf_file'), $edition);

        if (! $success) {
            $edition->forceDelete();
            return back()->withInput()->withErrors(['pdf_file' => 'పీడీఎఫ్ పేజీలను ప్రాసెస్ చేయడం విఫలమైంది. దయచేసి మళ్లీ ప్రయత్నించండి.']);
        }

        $publishMode = $request->input('publish_mode', 'publish_now');

        if ($publishMode === 'publish_now' && (Auth::user()?->canPublish() ?? true)) {
            $edition->status = 'published';
            $edition->published_at = now();
            $edition->approved_by = Auth::id();
            $edition->save();

            $edition->recordWorkflow('publish', 'draft', 'published', 'Published live immediately on upload', Auth::id());
            $successMsg = app()->getLocale() === 'en'
                ? "Edition successfully uploaded and published live! Total {$edition->total_pages} pages are now accessible to readers."
                : "సంచిక విజయవంతంగా అప్‌లోడ్ చేయబడి లైవ్‌లోకి వెళ్ళింది! మొత్తం {$edition->total_pages} పేజీలు రీడర్లకు అందుబాటులో ఉన్నాయి.";
        } elseif ($publishMode === 'schedule' && (Auth::user()?->canPublish() ?? true)) {
            $scheduledAt = Carbon::parse($dateString)->setTime(5, 0, 0);
            $edition->status = 'scheduled';
            $edition->scheduled_at = $scheduledAt;
            $edition->approved_by = Auth::id();
            $edition->save();

            $edition->recordWorkflow('schedule', 'draft', 'scheduled', 'Scheduled on upload for 5:00 AM IST on ' . $dateString, Auth::id());
            $successMsg = app()->getLocale() === 'en'
                ? "Edition uploaded and scheduled for 5:00 AM on {$dateString}. Total {$edition->total_pages} pages ready."
                : "సంచిక విజయవంతంగా అప్‌లోడ్ చేయబడింది మరియు {$dateString} ఉదయం 5:00 AM కి షెడ్యూల్ చేయబడింది. మొత్తం {$edition->total_pages} పేజీలు సిద్ధమయ్యాయి.";
        } else {
            $edition->recordWorkflow('upload', null, 'draft', 'Initial PDF uploaded and pages extracted as draft', Auth::id());
            $successMsg = app()->getLocale() === 'en'
                ? "Edition saved as draft. Total {$edition->total_pages} pages ready for review."
                : "సంచిక విజయవంతంగా అప్‌లోడ్ చేయబడింది (డ్రాఫ్ట్). మొత్తం {$edition->total_pages} పేజీలు సిద్ధమయ్యాయి.";
        }

        return redirect()->route('admin.editions.edit', $edition)
            ->with('success', $successMsg);
    }

    /**
     * Visual Edition Editor & Page Manager
     */
    public function edit(EpaperEdition $edition)
    {
        $edition->load(['region', 'pages' => fn ($q) => $q->orderBy('page_number'), 'workflowLogs.user']);
        $regions = EpaperRegion::active()->get();

        return view('admin.editions.edit', compact('edition', 'regions'));
    }

    /**
     * Update Metadata
     */
    public function update(Request $request, EpaperEdition $edition)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'volume_number' => ['nullable', 'string', 'max:50'],
            'issue_number' => ['nullable', 'string', 'max:50'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $edition->update($validated);
        $edition->recordWorkflow('update_metadata', $edition->status, $edition->status, 'Updated edition metadata', Auth::id());

        return back()->with('success', 'సంచిక వివరాలు విజయవంతంగా భద్రపరచబడ్డాయి.');
    }

    /**
     * Submit Draft for Review
     */
    public function submitApproval(EpaperEdition $edition)
    {
        if ($edition->pages->isEmpty() || $edition->total_pages === 0) {
            return back()->withErrors(['error' => app()->getLocale() === 'en' ? 'Cannot submit an edition with no pages.' : 'పేజీలు లేని సంచికను ఆమోదం కొరకు పంపలేరు.']);
        }

        $fromStatus = $edition->status;
        $edition->update(['status' => 'pending_approval']);
        $edition->recordWorkflow('submit_approval', $fromStatus, 'pending_approval', 'Submitted for editorial review', Auth::id());

        return back()->with('success', app()->getLocale() === 'en' ? 'Edition submitted for editorial review.' : 'సంచిక ఆమోదం కొరకు సమర్పించబడింది.');
    }

    /**
     * Approve Edition & Optionally Schedule for 5:00 AM IST
     */
    public function approve(Request $request, EpaperEdition $edition)
    {
        $action = $request->input('publish_action', 'schedule_morning');
        $notes = $request->input('notes', 'Approved by editorial desk');

        $fromStatus = $edition->status;

        if ($action === 'publish_now') {
            $edition->update([
                'status' => 'published',
                'approved_by' => Auth::id(),
                'published_at' => now(),
            ]);
            $edition->recordWorkflow('approve_and_publish', $fromStatus, 'published', $notes, Auth::id());
            return back()->with('success', app()->getLocale() === 'en' ? 'Edition approved and published live!' : 'సంచిక ఆమోదించబడి లైవ్ చేయబడింది.');
        }

        // Default: Schedule for 5:00 AM IST
        $editionDate = Carbon::parse($edition->edition_date);
        $scheduledTime = $editionDate->copy()->setTime(5, 0, 0);

        $edition->update([
            'status' => 'scheduled',
            'approved_by' => Auth::id(),
            'scheduled_at' => $scheduledTime,
        ]);

        $edition->recordWorkflow('approve_and_schedule', $fromStatus, 'scheduled', "Approved and scheduled for {$scheduledTime->format('d M Y, h:i A')} IST. Notes: {$notes}", Auth::id());

        return back()->with('success', app()->getLocale() === 'en' ? 'Edition approved and scheduled for release.' : 'సంచిక ఆమోదించబడింది. షెడ్యూల్ సమయానికి ఆటోమేటిక్‌గా ప్రచురించబడుతుంది.');
    }

    /**
     * Reject Edition with Reason
     */
    public function reject(Request $request, EpaperEdition $edition)
    {
        $reason = $request->input('reason', 'Editorial revisions required');
        $fromStatus = $edition->status;

        $edition->update(['status' => 'draft']);
        $edition->recordWorkflow('reject', $fromStatus, 'draft', "తిరస్కరించబడింది: {$reason}", Auth::id());

        return back()->with('warning', "సంచిక తిరస్కరించబడింది (కారణం: {$reason}). డ్రాఫ్ట్‌గా మార్చబడింది.");
    }

    /**
     * Immediate Live Publication
     */
    public function publishNow(EpaperEdition $edition)
    {
        if ($edition->pages->isEmpty() || $edition->total_pages === 0) {
            return back()->withErrors(['error' => 'పేజీలు ప్రాసెస్ కాని సంచికను ప్రచురించలేరు.']);
        }

        $fromStatus = $edition->status;
        $edition->update([
            'status' => 'published',
            'approved_by' => $edition->approved_by ?: Auth::id(),
            'published_at' => now(),
        ]);

        $edition->recordWorkflow('publish_now', $fromStatus, 'published', 'Manual 1-click publication', Auth::id());

        return back()->with('success', app()->getLocale() === 'en' ? 'Edition has been published live!' : 'సంచిక ప్రత్యక్షంగా (Live) ప్రచురించబడింది.');
    }

    /**
     * Unpublish Edition (Revert from Published or Scheduled back to Draft)
     */
    public function unpublish(EpaperEdition $edition)
    {
        $fromStatus = $edition->status;
        $edition->update([
            'status' => 'draft',
            'published_at' => null,
            'scheduled_at' => null,
        ]);

        $edition->recordWorkflow('unpublish', $fromStatus, 'draft', 'Reverted edition to draft by administrator', Auth::id());

        return back()->with('info', app()->getLocale() === 'en' ? 'Edition has been reverted to draft.' : 'సంచిక విజయవంతంగా డ్రాఫ్ట్‌గా మార్చబడింది.');
    }

    /**
     * Reorder Page Numbers
     */
    public function reorderPages(Request $request, EpaperEdition $edition)
    {
        $pageId = $request->input('page_id');
        $direction = $request->input('direction'); // 'up' or 'down'

        $page = EpaperPage::where('edition_id', $edition->id)->findOrFail($pageId);
        $currentNumber = $page->page_number;

        if ($direction === 'up' && $currentNumber > 1) {
            $swapPage = EpaperPage::where('edition_id', $edition->id)
                ->where('page_number', $currentNumber - 1)
                ->first();

            if ($swapPage) {
                $swapPage->update(['page_number' => 999]); // temp
                $page->update(['page_number' => $currentNumber - 1, 'sort_order' => $currentNumber - 1]);
                $swapPage->update(['page_number' => $currentNumber, 'sort_order' => $currentNumber]);
            }
        } elseif ($direction === 'down' && $currentNumber < $edition->total_pages) {
            $swapPage = EpaperPage::where('edition_id', $edition->id)
                ->where('page_number', $currentNumber + 1)
                ->first();

            if ($swapPage) {
                $swapPage->update(['page_number' => 999]); // temp
                $page->update(['page_number' => $currentNumber + 1, 'sort_order' => $currentNumber + 1]);
                $swapPage->update(['page_number' => $currentNumber, 'sort_order' => $currentNumber]);
            }
        }

        $edition->recordWorkflow('reorder_pages', $edition->status, $edition->status, "Reordered page {$currentNumber} {$direction}", Auth::id());

        return back()->with('success', 'పేజీల క్రమం మార్చబడింది.');
    }

    /**
     * Delete Edition & Storage Files Permanently
     */
    public function destroy(EpaperEdition $edition)
    {
        $title = $edition->title;
        $edition->recordWorkflow('delete', $edition->status, 'deleted', 'Edition deleted permanently', Auth::id());

        // Clean up page image and thumbnail files
        foreach ($edition->pages as $page) {
            if ($page->image_path && Storage::disk('public')->exists($page->image_path)) {
                Storage::disk('public')->delete($page->image_path);
            }
            if ($page->thumbnail_path && Storage::disk('public')->exists($page->thumbnail_path)) {
                Storage::disk('public')->delete($page->thumbnail_path);
            }
            if ($page->page_pdf_path && Storage::disk('public')->exists($page->page_pdf_path)) {
                Storage::disk('public')->delete($page->page_pdf_path);
            }
        }

        if ($edition->master_pdf_path && Storage::disk('public')->exists($edition->master_pdf_path)) {
            Storage::disk('public')->delete($edition->master_pdf_path);
        }

        $edition->forceDelete();

        return redirect()->route('admin.editions.index')->with('success', "సంచిక '{$title}' విజయవంతంగా తొలగించబడింది.");
    }
}
