<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EpaperEdition;
use App\Services\ReadershipAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadershipApiController extends Controller
{
    public function __construct(
        protected ReadershipAnalyticsService $analyticsService
    ) {}

    /**
     * Get aggregate today's readership for public header & widgets.
     */
    public function getToday(Request $request): JsonResponse
    {
        $editionId = $request->query('edition_id') ? (int) $request->query('edition_id') : null;
        $stats = $this->analyticsService->getTodayReadership($editionId);

        return response()->json($stats);
    }

    /**
     * Record a client-side reader event (e.g. reader open, page navigation, PDF download).
     */
    public function recordEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string|in:edition_open,page_view,page_nav,pdf_download,reader_open',
            'edition_id' => 'nullable|integer|exists:epaper_editions,id',
            'page_number' => 'nullable|integer|min:1|max:200',
        ]);

        $edition = ! empty($validated['edition_id'])
            ? EpaperEdition::find($validated['edition_id'])
            : null;

        $event = $this->analyticsService->recordEvent(
            $request,
            $validated['event_type'],
            $edition,
            $validated['page_number'] ?? null
        );

        return response()->json([
            'success' => true,
            'recorded' => $event !== null,
        ]);
    }
}
