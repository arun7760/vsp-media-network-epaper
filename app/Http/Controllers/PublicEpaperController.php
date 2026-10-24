<?php

namespace App\Http\Controllers;

use App\Models\EpaperEdition;
use App\Models\EpaperPage;
use App\Models\EpaperRegion;
use App\Services\ReadershipAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicEpaperController extends Controller
{
    public function __construct(
        protected ReadershipAnalyticsService $analyticsService
    ) {}
    /**
     * E-Paper Public Homepage - Today's Edition & Regional Showcase
     */
    public function index(Request $request)
    {
        $regions = EpaperRegion::active()->get();
        $selectedRegionSlug = $request->query('region', 'main');
        $selectedRegion = $regions->firstWhere('slug', $selectedRegionSlug) ?: $regions->first();

        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : Carbon::today();

        // Find edition for selected region and date
        $edition = EpaperEdition::with(['region', 'pages'])
            ->published()
            ->where('region_id', $selectedRegion?->id)
            ->whereDate('edition_date', $selectedDate)
            ->first();

        // Fallback: If no edition for selected date, fetch the latest published edition for this region
        if (! $edition && $selectedRegion) {
            $edition = EpaperEdition::with(['region', 'pages'])
                ->published()
                ->where('region_id', $selectedRegion->id)
                ->latest('edition_date')
                ->first();
        }

        // Today's editions across other active regions
        $otherEditions = EpaperEdition::with('region')
            ->published()
            ->whereHas('region', fn ($q) => $q->where('is_active', true))
            ->whereDate('edition_date', $edition?->edition_date ?? Carbon::today())
            ->when($edition, fn ($q) => $q->where('id', '!=', $edition->id))
            ->get();

        // Recent editions for the archive strip (last 7 days)
        $recentEditions = EpaperEdition::with('region')
            ->published()
            ->latest('edition_date')
            ->take(10)
            ->get()
            ->unique(fn ($e) => $e->edition_date->toDateString());

        if ($edition) {
            $this->analyticsService->recordEvent($request, 'edition_open', $edition);
        }

        return view('epaper.home', compact(
            'regions',
            'selectedRegion',
            'selectedDate',
            'edition',
            'otherEditions',
            'recentEditions'
        ));
    }

    /**
     * Interactive Broadsheet Newspaper Reader
     */
    public function reader(EpaperEdition $edition, int $pageNumber = 1)
    {
        // Enforce access control for non-published drafts
        if (! $edition->isPublished() && ! auth()->check()) {
            abort(404, 'This newspaper edition is not yet published.');
        }

        $edition->load(['region', 'pages' => fn ($q) => $q->orderBy('page_number')]);

        $totalPages = $edition->pages->count();
        if ($totalPages === 0) {
            $pageNumber = 1;
            $currentPage = null;
        } else {
            $pageNumber = max(1, min($pageNumber, $totalPages));
            $currentPage = $edition->pages->firstWhere('page_number', $pageNumber) ?: $edition->pages->first();
        }

        // Record first-party readership event
        $this->analyticsService->recordEvent(request(), 'edition_open', $edition, $pageNumber);

        $prevPageNumber = ($pageNumber > 1) ? $pageNumber - 1 : null;
        $nextPageNumber = ($pageNumber < $totalPages) ? $pageNumber + 1 : null;

        // All active editions published on the same date for regional switching inside reader
        $siblingEditions = EpaperEdition::with('region')
            ->published()
            ->whereHas('region', fn ($q) => $q->where('is_active', true))
            ->whereDate('edition_date', $edition->edition_date)
            ->get();

        return view('epaper.reader', compact(
            'edition',
            'currentPage',
            'pageNumber',
            'totalPages',
            'prevPageNumber',
            'nextPageNumber',
            'siblingEditions'
        ));
    }

    /**
     * Date-Based Archive Browser & Calendar Index
     */
    public function archive(Request $request)
    {
        $regions = EpaperRegion::active()->get();
        $selectedRegionId = $request->query('region_id');

        $selectedMonth = $request->query('month', Carbon::now()->month);
        $selectedYear = $request->query('year', Carbon::now()->year);

        $query = EpaperEdition::with(['region', 'pages'])
            ->published()
            ->when($selectedRegionId, fn ($q) => $q->where('region_id', $selectedRegionId))
            ->when($selectedYear, fn ($q) => $q->whereYear('edition_date', $selectedYear))
            ->when($selectedMonth, fn ($q) => $q->whereMonth('edition_date', $selectedMonth))
            ->latest('edition_date');

        $editions = $query->paginate(12)->withQueryString();

        // Published dates for active calendar markers
        $publishedDates = EpaperEdition::published()
            ->whereYear('edition_date', $selectedYear)
            ->whereMonth('edition_date', $selectedMonth)
            ->pluck('edition_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->unique()
            ->values();

        return view('epaper.archive', compact(
            'regions',
            'selectedRegionId',
            'selectedMonth',
            'selectedYear',
            'editions',
            'publishedDates'
        ));
    }

    /**
     * Download Full Edition PDF
     */
    public function downloadPdf(EpaperEdition $edition)
    {
        if (! $edition->isPublished() && ! auth()->check()) {
            abort(403);
        }

        $edition->increment('downloads_count');
        $this->analyticsService->recordEvent(request(), 'pdf_download', $edition);

        if ($edition->master_pdf_path && Storage::disk('public')->exists($edition->master_pdf_path)) {
            return Storage::disk('public')->download(
                $edition->master_pdf_path,
                "VSP_TIMES_{$edition->edition_date->format('Y-m-d')}_{$edition->region?->slug}.pdf"
            );
        }

        return back()->with('info', 'ఈ సంచిక పూర్తి పీడీఎఫ్ త్వరలో అందుబాటులో ఉంటుంది.');
    }
}
