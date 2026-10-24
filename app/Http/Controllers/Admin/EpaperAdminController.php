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
     * Manage Editions List with Status & Region Filters
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $regionId = $request->query('region_id');
        $date = $request->query('date');

        $query = EpaperEdition::with(['region', 'creator', 'approver'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($regionId, fn ($q) => $q->where('region_id', $regionId))
            ->when($date, fn ($q) => $q->whereDate('edition_date', $date))
            ->latest('edition_date');

        $editions = $query->paginate(15)->withQueryString();
        $regions = EpaperRegion::active()->get();

        return view('admin.editions.index', compact('editions', 'regions', 'status', 'regionId', 'date'));
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

        $edition->recordWorkflow('upload', null, 'draft', 'Initial PDF uploaded and pages extracted', Auth::id());

        return redirect()->route('admin.editions.edit', $edition)
            ->with('success', "సంచిక విజయవంతంగా అప్‌లోడ్ చేయబడింది. మొత్తం {$edition->total_pages} పేజీలు సిద్ధమయ్యాయి.");
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
            return back()->withErrors(['error' => 'పేజీలు లేని సంచికను ఆమోదం కొరకు పంపలేరు.']);
        }

        $fromStatus = $edition->status;
        $edition->update(['status' => 'pending_approval']);
        $edition->recordWorkflow('submit_approval', $fromStatus, 'pending_approval', 'Submitted for editorial review', Auth::id());

        return back()->with('success', 'సంచిక ఆమోదం కొరకు సమర్పించబడింది.');
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
            return back()->with('success', 'సంచిక ఆమోదించబడి లైవ్ చేయబడింది.');
        }

        // Default: Schedule for upcoming 5:00 AM IST
        $scheduledTime = Carbon::parse($edition->edition_date)->setTime(5, 0, 0);

        $edition->update([
            'status' => 'scheduled',
            'approved_by' => Auth::id(),
            'scheduled_at' => $scheduledTime,
        ]);

        $edition->recordWorkflow('approve_and_schedule', $fromStatus, 'scheduled', "Approved and scheduled for {$scheduledTime->format('d M Y, h:i A')} IST. Notes: {$notes}", Auth::id());

        return back()->with('success', 'సంచిక ఆమోదించబడింది. రేపు ఉదయం 5:00 గంటలకు ఆటోమేటిక్‌గా ప్రచురించబడుతుంది.');
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

        return back()->with('success', 'సంచిక ప్రత్యక్షంగా (Live) ప్రచురించబడింది.');
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
