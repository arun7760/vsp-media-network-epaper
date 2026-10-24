<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReadershipAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(
        protected ReadershipAnalyticsService $analyticsService
    ) {}

    /**
     * Display Reader Analytics Dashboard.
     */
    public function index(Request $request): View
    {
        $period = $request->query('period', 'today');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $metrics = $this->analyticsService->getAdminMetrics($period, $startDate, $endDate);

        return view('admin.analytics.index', compact('metrics', 'period'));
    }
}
