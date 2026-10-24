@extends('layouts.admin')

@section('title', __('admin.analytics') . ' | VSP TIMES Control Panel')
@section('header_title', __('admin.analytics'))

@push('styles')
<style>
    .analytics-filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #FFFFFF;
        padding: 16px 20px;
        border-radius: 10px;
        border: 1px solid var(--admin-border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        margin-bottom: 24px;
    }
    .period-pills {
        display: inline-flex;
        background: #F1F5F9;
        border-radius: 8px;
        padding: 3px;
        border: 1px solid var(--admin-border);
    }
    .period-pill {
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 6px;
        text-decoration: none;
        color: var(--admin-muted);
        transition: all 0.2s;
    }
    .period-pill.active {
        background: var(--admin-navy);
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(10, 29, 55, 0.25);
    }
    .period-pill:hover:not(.active) {
        color: var(--admin-navy);
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        padding: 22px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    }
    .kpi-accent {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .kpi-label {
        font-size: 13px;
        color: var(--admin-muted);
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kpi-val {
        font-size: 32px;
        font-weight: 800;
        color: var(--admin-navy);
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .kpi-subtext {
        font-size: 12px;
        color: #94A3B8;
        margin-top: 6px;
    }

    .chart-container {
        background: #FFFFFF;
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 28px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .chart-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--admin-navy);
    }

    .breakdown-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    @media (max-width: 900px) {
        .breakdown-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-bar-row {
        margin-bottom: 14px;
    }
    .stat-bar-labels {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        color: var(--admin-text);
        margin-bottom: 4px;
    }
    .stat-progress-track {
        height: 8px;
        background: #F1F5F9;
        border-radius: 999px;
        overflow: hidden;
    }
    .stat-progress-fill {
        height: 100%;
        border-radius: 999px;
    }

    .integrations-card {
        background: linear-gradient(135deg, #0A1D37 0%, #001E4D 100%);
        color: #FFFFFF;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 28px;
    }
</style>
@endpush

@section('content')

    <!-- Date Period Filter Bar -->
    <div class="analytics-filter-bar">
        <div class="period-pills">
            <a href="{{ route('admin.analytics.index', ['period' => 'today']) }}" class="period-pill {{ $period === 'today' ? 'active' : '' }}">
                {{ app()->getLocale() === 'en' ? 'Today' : 'ఈ రోజు' }}
            </a>
            <a href="{{ route('admin.analytics.index', ['period' => 'yesterday']) }}" class="period-pill {{ $period === 'yesterday' ? 'active' : '' }}">
                {{ app()->getLocale() === 'en' ? 'Yesterday' : 'నిన్న' }}
            </a>
            <a href="{{ route('admin.analytics.index', ['period' => '7days']) }}" class="period-pill {{ $period === '7days' ? 'active' : '' }}">
                {{ app()->getLocale() === 'en' ? 'Last 7 Days' : 'గత 7 రోజులు' }}
            </a>
            <a href="{{ route('admin.analytics.index', ['period' => '30days']) }}" class="period-pill {{ $period === '30days' ? 'active' : '' }}">
                {{ app()->getLocale() === 'en' ? 'Last 30 Days' : 'గత 30 రోజులు' }}
            </a>
        </div>

        <div style="font-size: 13px; color: var(--admin-muted); font-weight: 600;">
            <i class="fa-regular fa-calendar-check" style="color: var(--admin-blue);"></i>
            <span>{{ app()->getLocale() === 'en' ? 'Reporting Period:' : 'నివేదిక సమయం:' }} <strong>{{ $metrics['start_date'] }}</strong> {{ app()->getLocale() === 'en' ? 'to' : 'నుండి' }} <strong>{{ $metrics['end_date'] }}</strong> (Asia/Kolkata)</span>
        </div>
    </div>

    <!-- 6 KPI Metrics Summary Grid -->
    <div class="kpi-grid">
        <!-- Today's Unique Readers -->
        <div class="kpi-card">
            <div class="kpi-accent" style="background: linear-gradient(90deg, #D91B24, #B80F17);"></div>
            <div class="kpi-label">
                <span>{{ app()->getLocale() === 'en' ? "Total Readers" : 'మొత్తం పాఠకులు' }}</span>
                <i class="fa-solid fa-users" style="color: var(--admin-red);"></i>
            </div>
            <div class="kpi-val">{{ number_format($metrics['unique_readers']) }}</div>
            <div class="kpi-subtext">{{ app()->getLocale() === 'en' ? 'Distinct verified readers' : 'నిర్ధారించబడిన విభిన్న పాఠకులు' }}</div>
        </div>

        <!-- Total Edition Views -->
        <div class="kpi-card">
            <div class="kpi-accent" style="background: linear-gradient(90deg, #0047AB, #0A1D37);"></div>
            <div class="kpi-label">
                <span>{{ app()->getLocale() === 'en' ? 'Edition Views' : 'ఎడిషన్ వీక్షణలు' }}</span>
                <i class="fa-solid fa-book-open" style="color: var(--admin-blue);"></i>
            </div>
            <div class="kpi-val">{{ number_format($metrics['total_views']) }}</div>
            <div class="kpi-subtext">{{ app()->getLocale() === 'en' ? 'Full editions opened' : 'మొత్తం తెరిచిన సంచికలు' }}</div>
        </div>

        <!-- Total Website Visitors -->
        <div class="kpi-card">
            <div class="kpi-accent" style="background: linear-gradient(90deg, #059669, #10B981);"></div>
            <div class="kpi-label">
                <span>{{ app()->getLocale() === 'en' ? 'Portal Visitors' : 'వెబ్‌సైట్ సందర్శకులు' }}</span>
                <i class="fa-solid fa-globe" style="color: #059669;"></i>
            </div>
            <div class="kpi-val">{{ number_format($metrics['total_visitors']) }}</div>
            <div class="kpi-subtext">{{ app()->getLocale() === 'en' ? 'Unique visitors across site' : 'సైట్ మొత్తం సందర్శకులు' }}</div>
        </div>

        <!-- Total Page Views -->
        <div class="kpi-card">
            <div class="kpi-accent" style="background: linear-gradient(90deg, #7C3AED, #9333EA);"></div>
            <div class="kpi-label">
                <span>{{ app()->getLocale() === 'en' ? 'Page Impressions' : 'పేజీ వీక్షణలు' }}</span>
                <i class="fa-solid fa-file-lines" style="color: #7C3AED;"></i>
            </div>
            <div class="kpi-val">{{ number_format($metrics['total_page_views']) }}</div>
            <div class="kpi-subtext">{{ app()->getLocale() === 'en' ? 'Total pages read' : 'మొత్తం చదివిన పేజీలు' }}</div>
        </div>

        <!-- PDF Downloads -->
        <div class="kpi-card">
            <div class="kpi-accent" style="background: linear-gradient(90deg, #EA580C, #F97316);"></div>
            <div class="kpi-label">
                <span>{{ app()->getLocale() === 'en' ? 'PDF Downloads' : 'పీడీఎఫ్ డౌన్‌లోడ్స్' }}</span>
                <i class="fa-solid fa-file-pdf" style="color: #EA580C;"></i>
            </div>
            <div class="kpi-val">{{ number_format($metrics['pdf_downloads']) }}</div>
            <div class="kpi-subtext">{{ app()->getLocale() === 'en' ? 'Completed downloads' : 'పూర్తి పీడీఎఫ్ డౌన్‌లోడ్స్' }}</div>
        </div>

        <!-- Reader Engagement (Avg Pages / Reader) -->
        <div class="kpi-card">
            <div class="kpi-accent" style="background: linear-gradient(90deg, #0284C7, #06B6D4);"></div>
            <div class="kpi-label">
                <span>{{ app()->getLocale() === 'en' ? 'Reader Engagement' : 'సగటు నిమగ్నత' }}</span>
                <i class="fa-solid fa-chart-simple" style="color: #0284C7;"></i>
            </div>
            <div class="kpi-val">{{ $metrics['avg_pages_per_session'] }} <span style="font-size: 16px; font-weight: 600; color: #94A3B8;">{{ app()->getLocale() === 'en' ? 'pages/reader' : 'పేజీలు/పాఠకుడు' }}</span></div>
            <div class="kpi-subtext">{{ app()->getLocale() === 'en' ? 'Depth of broadsheet reading' : 'సంచిక పఠన సగటు' }}</div>
        </div>
    </div>

    <!-- Readership Trend Visualization -->
    <div class="chart-container">
        <div class="chart-header">
            <div>
                <div class="chart-title">{{ app()->getLocale() === 'en' ? 'Daily Readership Trend' : 'రోజువారీ పాఠకుల పఠన గ్రాఫ్' }}</div>
                <div style="font-size: 13px; color: var(--admin-muted);">{{ app()->getLocale() === 'en' ? 'Unique readers vs total edition views over the period' : 'ఎంచుకున్న కాలంలో నిర్ధారించబడిన పాఠకులు vs మొత్తం వీక్షణలు' }}</div>
            </div>
            <div style="display: flex; gap: 16px; font-size: 12px; font-weight: 700;">
                <span style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: var(--admin-red); border-radius: 3px;"></span> {{ app()->getLocale() === 'en' ? 'Unique Readers' : 'పాఠకులు' }}</span>
                <span style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: var(--admin-blue); border-radius: 3px;"></span> {{ app()->getLocale() === 'en' ? 'Total Views' : 'వీక్షణలు' }}</span>
            </div>
        </div>

        @if($metrics['daily_trends']->isEmpty())
            <div style="padding: 40px; text-align: center; color: var(--admin-muted);">
                <i class="fa-regular fa-chart-bar" style="font-size: 36px; margin-bottom: 8px;"></i>
                <p>{{ app()->getLocale() === 'en' ? 'No reader events recorded yet for this date range. As readers access the paper, daily trends will appear here.' : 'ఈ సమయానికి ఎటువంటి ఈవెంట్లు నమోదు కాలేదు. పాఠకులు పేపర్ చదవగానే వివరాలు కనిపిస్తాయి.' }}</p>
            </div>
        @else
            <div style="display: flex; align-items: flex-end; gap: 16px; height: 220px; padding-top: 20px; border-bottom: 2px solid var(--admin-border); overflow-x: auto;">
                @php 
                    $maxVal = max($metrics['daily_trends']->max('views') ?: 1, 1);
                @endphp
                @foreach($metrics['daily_trends'] as $day)
                    @php 
                        $readersHeight = max(round(($day->readers / $maxVal) * 160), 8);
                        $viewsHeight = max(round(($day->views / $maxVal) * 160), 12);
                    @endphp
                    <div style="flex: 1; min-width: 60px; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                        <div style="display: flex; align-items: flex-end; gap: 4px; width: 100%; justify-content: center;">
                            <div style="height: {{ $readersHeight }}px; width: 18px; background: var(--admin-red); border-radius: 4px 4px 0 0;" title="{{ app()->getLocale() === 'en' ? 'Readers' : 'పాఠకులు' }}: {{ $day->readers }}"></div>
                            <div style="height: {{ $viewsHeight }}px; width: 18px; background: var(--admin-blue); border-radius: 4px 4px 0 0;" title="{{ app()->getLocale() === 'en' ? 'Views' : 'వీక్షణలు' }}: {{ $day->views }}"></div>
                        </div>
                        <span style="font-size: 11px; color: var(--admin-muted); margin-top: 8px; font-weight: 600; white-space: nowrap;">{{ date('d M', strtotime($day->event_date)) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Traffic Sources & Devices Breakdown Grid -->
    <div class="breakdown-grid">
        <!-- Traffic Sources -->
        <div class="card" style="margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--admin-navy); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-route" style="color: var(--admin-blue);"></i> {{ app()->getLocale() === 'en' ? 'Traffic Sources' : 'ట్రాఫిక్ మూలాలు' }}
            </h3>
            @php $totalSourceCount = max($metrics['traffic_sources']->sum('count'), 1); @endphp
            @forelse($metrics['traffic_sources'] as $source)
                @php $pct = round(($source->count / $totalSourceCount) * 100); @endphp
                <div class="stat-bar-row">
                    <div class="stat-bar-labels">
                        <span>{{ ucfirst($source->traffic_source) }}</span>
                        <span>{{ number_format($source->count) }} ({{ $pct }}%)</span>
                    </div>
                    <div class="stat-progress-track">
                        <div class="stat-progress-fill" style="width: {{ $pct }}%; background: var(--admin-blue);"></div>
                    </div>
                </div>
            @empty
                <p style="color: var(--admin-muted); font-size: 13px;">{{ app()->getLocale() === 'en' ? 'No source data recorded yet.' : 'మూలాల వివరాలు నమోదు కాలేదు.' }}</p>
            @endforelse
        </div>

        <!-- Devices Breakdown -->
        <div class="card" style="margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--admin-navy); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-mobile-screen-button" style="color: var(--admin-red);"></i> {{ app()->getLocale() === 'en' ? 'Device Distribution' : 'పరికరం వారీగా' }}
            </h3>
            @php $totalDevCount = max($metrics['device_breakdown']->sum('count'), 1); @endphp
            @forelse($metrics['device_breakdown'] as $dev)
                @php $pct = round(($dev->count / $totalDevCount) * 100); @endphp
                <div class="stat-bar-row">
                    <div class="stat-bar-labels">
                        <span>{{ ucfirst($dev->device_type) }}</span>
                        <span>{{ number_format($dev->count) }} ({{ $pct }}%)</span>
                    </div>
                    <div class="stat-progress-track">
                        <div class="stat-progress-fill" style="width: {{ $pct }}%; background: var(--admin-red);"></div>
                    </div>
                </div>
            @empty
                <p style="color: var(--admin-muted); font-size: 13px;">{{ app()->getLocale() === 'en' ? 'No device data recorded yet.' : 'పరికరం వివరాలు నమోదు కాలేదు.' }}</p>
            @endforelse
        </div>
    </div>

    <!-- Edition-wise Statistics Table -->
    <div class="card">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--admin-navy); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-newspaper" style="color: var(--admin-navy);"></i> {{ app()->getLocale() === 'en' ? 'Edition Performance Comparison' : 'సంచికల వారీ పనితీరు పోలిక' }}
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--admin-border); text-align: left; color: var(--admin-navy);">
                        <th style="padding: 12px 14px;">{{ app()->getLocale() === 'en' ? 'Edition / Broadsheet' : 'సంచిక శీర్షిక' }}</th>
                        <th style="padding: 12px 14px;">{{ app()->getLocale() === 'en' ? 'Region' : 'ప్రాంతం' }}</th>
                        <th style="padding: 12px 14px;">{{ app()->getLocale() === 'en' ? 'Date' : 'తేదీ' }}</th>
                        <th style="padding: 12px 14px; text-align: right;">{{ app()->getLocale() === 'en' ? 'Period Readers' : 'కాలపు పాఠకులు' }}</th>
                        <th style="padding: 12px 14px; text-align: right;">{{ app()->getLocale() === 'en' ? 'Period Views' : 'కాలపు వీక్షణలు' }}</th>
                        <th style="padding: 12px 14px; text-align: right;">{{ app()->getLocale() === 'en' ? 'PDF Downloads' : 'పీడీఎఫ్ డౌన్‌లోడ్స్' }}</th>
                        <th style="padding: 12px 14px; text-align: right;">{{ app()->getLocale() === 'en' ? 'Historical All-Time' : 'ఆల్‌టైమ్ వీక్షణలు' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metrics['edition_stats'] as $row)
                        <tr style="border-bottom: 1px solid var(--admin-border);">
                            <td style="padding: 12px 14px; font-weight: 700; color: var(--admin-navy);">
                                {{ $row->title }}
                            </td>
                            <td style="padding: 12px 14px;">
                                <span style="background: #EFF6FF; color: var(--admin-blue); padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">
                                    {{ $row->region_name ?? 'All' }}
                                </span>
                            </td>
                            <td style="padding: 12px 14px; color: var(--admin-muted);">
                                {{ date('d M Y', strtotime($row->edition_date)) }}
                            </td>
                            <td style="padding: 12px 14px; text-align: right; font-weight: 700; color: var(--admin-red);">
                                {{ number_format($row->period_readers) }}
                            </td>
                            <td style="padding: 12px 14px; text-align: right; font-weight: 700; color: var(--admin-blue);">
                                {{ number_format($row->period_views) }}
                            </td>
                            <td style="padding: 12px 14px; text-align: right; font-weight: 700; color: #EA580C;">
                                {{ number_format($row->period_downloads) }}
                            </td>
                            <td style="padding: 12px 14px; text-align: right; color: var(--admin-muted);">
                                {{ number_format($row->historical_views) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 24px; text-align: center; color: var(--admin-muted);">
                                {{ app()->getLocale() === 'en' ? 'No edition records available.' : 'సంచికల వివరాలు అందుబాటులో లేవు.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Integrations & Tag Status Card -->
    <div class="integrations-card">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-satellite-dish" style="color: var(--admin-red);"></i> {{ app()->getLocale() === 'en' ? 'Analytics & Advertising Tag Status' : 'అనలిటిక్స్ & ప్రకటనల ట్రాకింగ్ అనుసంధాన స్థితి' }}
        </h3>
        <p style="font-size: 13px; color: #CBD5E1; margin-bottom: 16px;">
            {{ app()->getLocale() === 'en' 
                ? 'Official integration slots configured. Tags automatically activate when container/measurement IDs are placed into your .env configuration file.' 
                : 'అధికారిక ట్రాకింగ్ స్లాట్‌లు సిద్ధంగా ఉన్నాయి. మీ .env ఫైల్‌లో ఐడీలను నమోదు చేయగానే ఆటోమేటిక్‌గా యాక్టివేట్ అవుతాయి.' }}
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
            <div style="background: rgba(255,255,255,0.08); padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                <div style="font-size: 12px; color: #94A3B8;">Google Tag Manager (GTM)</div>
                <div style="font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 8px;">
                    @if(config('analytics.gtm_container_id'))
                        <i class="fa-solid fa-circle-check" style="color: #10B981;"></i> <code>{{ config('analytics.gtm_container_id') }}</code>
                    @else
                        <i class="fa-solid fa-circle-info" style="color: #94A3B8;"></i> <span>Waiting for <code>GTM_CONTAINER_ID</code> in .env</span>
                    @endif
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.08); padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                <div style="font-size: 12px; color: #94A3B8;">Google Analytics 4 (GA4)</div>
                <div style="font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 8px;">
                    @if(config('analytics.ga4_measurement_id'))
                        <i class="fa-solid fa-circle-check" style="color: #10B981;"></i> <code>{{ config('analytics.ga4_measurement_id') }}</code>
                    @else
                        <i class="fa-solid fa-circle-info" style="color: #94A3B8;"></i> <span>Waiting for <code>GA4_MEASUREMENT_ID</code> in .env</span>
                    @endif
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.08); padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                <div style="font-size: 12px; color: #94A3B8;">Google Ads Conversion Tracking</div>
                <div style="font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 8px;">
                    @if(config('analytics.google_ads_conversion_id'))
                        <i class="fa-solid fa-circle-check" style="color: #10B981;"></i> <code>{{ config('analytics.google_ads_conversion_id') }}</code>
                    @else
                        <i class="fa-solid fa-circle-info" style="color: #94A3B8;"></i> <span>Waiting for <code>GOOGLE_ADS_CONVERSION_ID</code> in .env</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
