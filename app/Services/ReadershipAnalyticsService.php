<?php

namespace App\Services;

use App\Models\EpaperEdition;
use App\Models\EpaperReaderEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReadershipAnalyticsService
{
    /**
     * Get India Date for consistent day boundaries.
     */
    public function getTodayDate(): string
    {
        return Carbon::now('Asia/Kolkata')->toDateString();
    }

    /**
     * Generate an anonymous, privacy-conscious visitor token.
     * Never stores raw IPs or personal identification data.
     */
    public function generateVisitorToken(Request $request): string
    {
        $sessionId = $request->session()->getId() ?: 'guest-token';
        $today = $this->getTodayDate();
        $salt = config('app.key', 'vsp-times-secure-analytics-salt');

        return hash('sha256', "{$sessionId}_{$today}_{$salt}");
    }

    /**
     * Detect device type from User-Agent.
     */
    public function detectDeviceType(string $userAgent): string
    {
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || str_contains($ua, 'playbook') || str_contains($ua, 'silk')) {
            return 'tablet';
        }
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone') || str_contains($ua, 'ipod')) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Detect traffic source from Referer header.
     */
    public function detectTrafficSource(?string $referer): array
    {
        if (empty($referer)) {
            return ['traffic_source' => 'direct', 'referrer_host' => null];
        }

        $host = parse_url($referer, PHP_URL_HOST);
        if (! $host) {
            return ['traffic_source' => 'direct', 'referrer_host' => null];
        }

        $hostLower = strtolower($host);

        // Internal cross-domain referrals
        if (str_contains($hostLower, 'vspmedianetwork.com')) {
            return ['traffic_source' => 'main_portal', 'referrer_host' => $host];
        }

        // Search Engines
        if (str_contains($hostLower, 'google.') || str_contains($hostLower, 'bing.') || str_contains($hostLower, 'yahoo.') || str_contains($hostLower, 'duckduckgo.')) {
            return ['traffic_source' => 'google_search', 'referrer_host' => $host];
        }

        // Social Media
        if (str_contains($hostLower, 'facebook.') || str_contains($hostLower, 'fb.com') || str_contains($hostLower, 'twitter.') || str_contains($hostLower, 't.co') || str_contains($hostLower, 'x.com') || str_contains($hostLower, 'instagram.') || str_contains($hostLower, 'whatsapp.') || str_contains($hostLower, 'telegram.') || str_contains($hostLower, 'linkedin.')) {
            return ['traffic_source' => 'social', 'referrer_host' => $host];
        }

        return ['traffic_source' => 'referral', 'referrer_host' => $host];
    }

    /**
     * Check if a request is from an automated bot or crawler.
     */
    public function isBot(string $userAgent): bool
    {
        $ua = strtolower($userAgent);
        $botPatterns = [
            'bot', 'crawl', 'slurp', 'spider', 'mediapartners', 'lighthouse',
            'headlesschrome', 'curl', 'wget', 'postman', 'bytespider', 'yandex', 'baidu',
        ];

        foreach ($botPatterns as $pat) {
            if (str_contains($ua, $pat)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Record a first-party reader event.
     */
    public function recordEvent(Request $request, string $eventType, ?EpaperEdition $edition = null, ?int $pageNumber = null): ?EpaperReaderEvent
    {
        $userAgent = $request->userAgent() ?: 'unknown';

        // 1. Exclude Bots
        if ($this->isBot($userAgent)) {
            return null;
        }

        // 2. Exclude Admin & Staff Previews
        if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isReviewer())) {
            return null;
        }

        $visitorToken = $this->generateVisitorToken($request);
        $today = $this->getTodayDate();

        // 3. Deduplicate rapid duplicate hits within 15 seconds
        $debounceKey = "event_debounce_{$visitorToken}_{$eventType}_" . ($edition ? $edition->id : 'site') . "_{$pageNumber}";
        if (Cache::has($debounceKey)) {
            return null;
        }
        Cache::put($debounceKey, true, 15);

        $deviceType = $this->detectDeviceType($userAgent);
        $traffic = $this->detectTrafficSource($request->header('referer'));

        $event = EpaperReaderEvent::create([
            'edition_id' => $edition?->id,
            'visitor_token' => $visitorToken,
            'event_type' => $eventType,
            'page_number' => $pageNumber,
            'event_date' => $today,
            'device_type' => $deviceType,
            'traffic_source' => $traffic['traffic_source'],
            'referrer_host' => $traffic['referrer_host'],
        ]);

        // If edition_open, increment database views_count on the edition record
        if ($eventType === 'edition_open' && $edition) {
            $edition->increment('views_count');
        }

        return $event;
    }

    /**
     * Get aggregate readership for today (for public header & APIs).
     * Server-side cached for high performance.
     */
    public function getTodayReadership(?int $editionId = null): array
    {
        $today = $this->getTodayDate();
        $ttl = config('analytics.cache_ttl_seconds', 900);
        $cacheKey = "epaper_readership_today_{$today}_" . ($editionId ?: 'default');

        return Cache::remember($cacheKey, $ttl, function () use ($today, $editionId) {
            $query = EpaperReaderEvent::where('event_date', $today);

            if ($editionId) {
                $query->where('edition_id', $editionId);
            }

            // Distinct anonymous readers who opened the newspaper edition today
            $uniqueReaders = (clone $query)
                ->where('event_type', 'edition_open')
                ->distinct('visitor_token')
                ->count('visitor_token');

            // Total edition opens today
            $totalEditionViews = (clone $query)
                ->where('event_type', 'edition_open')
                ->count();

            // Total PDF downloads today
            $pdfDownloads = (clone $query)
                ->where('event_type', 'pdf_download')
                ->count();

            // Total website public visitors today
            $totalVisitors = EpaperReaderEvent::where('event_date', $today)
                ->distinct('visitor_token')
                ->count('visitor_token');

            return [
                'success' => true,
                'edition_id' => $editionId,
                'date' => $today,
                'today_readers' => $uniqueReaders,
                'today_views' => $totalEditionViews,
                'pdf_downloads' => $pdfDownloads,
                'total_visitors' => $totalVisitors,
                'formatted_readers' => $this->formatNumber($uniqueReaders),
                'formatted_views' => $this->formatNumber($totalEditionViews),
                'source' => 'first_party_verified',
            ];
        });
    }

    /**
     * Format numbers cleanly (e.g., 1,250 or 1.2K).
     */
    public function formatNumber(int $num): string
    {
        if ($num >= 1000000) {
            return round($num / 1000000, 1) . 'M';
        }
        if ($num >= 10000) {
            return round($num / 1000, 1) . 'K';
        }
        return number_format($num);
    }

    /**
     * Retrieve comprehensive reader analytics metrics for Admin Dashboard.
     */
    public function getAdminMetrics(string $period = 'today', ?string $startDate = null, ?string $endDate = null): array
    {
        $today = Carbon::now('Asia/Kolkata')->toDateString();

        switch ($period) {
            case 'yesterday':
                $start = Carbon::now('Asia/Kolkata')->subDay()->toDateString();
                $end = $start;
                break;
            case '7days':
                $start = Carbon::now('Asia/Kolkata')->subDays(6)->toDateString();
                $end = $today;
                break;
            case '30days':
                $start = Carbon::now('Asia/Kolkata')->subDays(29)->toDateString();
                $end = $today;
                break;
            case 'custom':
                $start = $startDate ?: Carbon::now('Asia/Kolkata')->subDays(7)->toDateString();
                $end = $endDate ?: $today;
                break;
            case 'today':
            default:
                $start = $today;
                $end = $today;
                break;
        }

        $baseQuery = EpaperReaderEvent::whereBetween('event_date', [$start, $end]);

        // 1. Core Summary Cards
        $uniqueReaders = (clone $baseQuery)
            ->where('event_type', 'edition_open')
            ->distinct('visitor_token')
            ->count('visitor_token');

        $totalViews = (clone $baseQuery)
            ->where('event_type', 'edition_open')
            ->count();

        $totalVisitors = (clone $baseQuery)
            ->distinct('visitor_token')
            ->count('visitor_token');

        $totalPageViews = (clone $baseQuery)
            ->whereIn('event_type', ['page_view', 'page_nav', 'edition_open'])
            ->count();

        $pdfDownloads = (clone $baseQuery)
            ->where('event_type', 'pdf_download')
            ->count();

        $pageNavigations = (clone $baseQuery)
            ->where('event_type', 'page_nav')
            ->count();

        $avgPagesPerSession = $uniqueReaders > 0 ? round(($totalPageViews) / $uniqueReaders, 1) : 0;

        // 2. Historical Daily Trends (for Chart)
        $dailyTrends = (clone $baseQuery)
            ->where('event_type', 'edition_open')
            ->select('event_date', DB::raw('COUNT(DISTINCT visitor_token) as readers'), DB::raw('COUNT(*) as views'))
            ->groupBy('event_date')
            ->orderBy('event_date', 'asc')
            ->get();

        // 3. Traffic Sources Breakdown
        $trafficSources = (clone $baseQuery)
            ->select('traffic_source', DB::raw('COUNT(*) as count'))
            ->groupBy('traffic_source')
            ->orderBy('count', 'desc')
            ->get();

        // 4. Device Breakdown
        $deviceBreakdown = (clone $baseQuery)
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();

        // 5. Edition-wise Statistics Table
        $editionStats = DB::table('epaper_editions')
            ->leftJoin('epaper_reader_events', function ($join) use ($start, $end) {
                $join->on('epaper_editions.id', '=', 'epaper_reader_events.edition_id')
                    ->whereBetween('epaper_reader_events.event_date', [$start, $end]);
            })
            ->leftJoin('epaper_regions', 'epaper_editions.region_id', '=', 'epaper_regions.id')
            ->select(
                'epaper_editions.id',
                'epaper_editions.title',
                'epaper_editions.edition_date',
                'epaper_editions.status',
                'epaper_editions.views_count as historical_views',
                'epaper_regions.name as region_name',
                DB::raw("COUNT(DISTINCT CASE WHEN epaper_reader_events.event_type = 'edition_open' THEN epaper_reader_events.visitor_token END) as period_readers"),
                DB::raw("COUNT(CASE WHEN epaper_reader_events.event_type = 'edition_open' THEN 1 END) as period_views"),
                DB::raw("COUNT(CASE WHEN epaper_reader_events.event_type = 'pdf_download' THEN 1 END) as period_downloads")
            )
            ->groupBy('epaper_editions.id', 'epaper_editions.title', 'epaper_editions.edition_date', 'epaper_editions.status', 'epaper_editions.views_count', 'epaper_regions.name')
            ->orderBy('epaper_editions.edition_date', 'desc')
            ->limit(20)
            ->get();

        return [
            'period' => $period,
            'start_date' => $start,
            'end_date' => $end,
            'unique_readers' => $uniqueReaders,
            'total_views' => $totalViews,
            'total_visitors' => $totalVisitors,
            'total_page_views' => $totalPageViews,
            'pdf_downloads' => $pdfDownloads,
            'page_navigations' => $pageNavigations,
            'avg_pages_per_session' => $avgPagesPerSession,
            'daily_trends' => $dailyTrends,
            'traffic_sources' => $trafficSources,
            'device_breakdown' => $deviceBreakdown,
            'edition_stats' => $editionStats,
        ];
    }
}
