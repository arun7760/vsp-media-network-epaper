@extends('layouts.admin')

@section('title', 'డ్యాష్‌బోర్డ్ | VSP TIMES E-Paper CMS')
@section('header_title', 'ఈ-పేపర్ ఎడిటోరియల్ డ్యాష్‌బోర్డ్')

@push('styles')
<style>
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    .metric-card {
        background: #FFFFFF;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        padding: 20px 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .metric-num {
        font-size: 26px;
        font-weight: 800;
        color: var(--admin-navy);
        line-height: 1.1;
    }
    .metric-label {
        font-size: 13px;
        color: var(--admin-muted);
        font-weight: 600;
    }

    .table-responsive {
        overflow-x: auto;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        text-align: left;
    }
    .data-table th {
        background: #F8FAFC;
        padding: 12px 16px;
        font-weight: 700;
        color: var(--admin-navy);
        border-bottom: 2px solid var(--admin-border);
    }
    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--admin-border);
        vertical-align: middle;
    }
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-published { background: #ECFDF5; color: #065F46; }
    .badge-scheduled { background: #EFF6FF; color: #1E40AF; }
    .badge-pending { background: #FFFBEB; color: #92400E; }
    .badge-draft { background: #F1F5F9; color: #475569; }
</style>
@endpush

@section('content')

    <!-- Metrics Grid -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(10, 29, 55, 0.08); color: var(--admin-navy);">
                <i class="fa-solid fa-newspaper"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['total_editions'] }}</div>
                <div class="metric-label">మొత్తం సంచికలు (Editions)</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['published_editions'] }}</div>
                <div class="metric-label">లైవ్ సంచికలు (Published)</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #F59E0B;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['pending_approvals'] }}</div>
                <div class="metric-label">ఆమోదం కోసం (Pending)</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['scheduled_editions'] }}</div>
                <div class="metric-label">షెడ్యూల్డ్ (5:00 AM IST)</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: rgba(212, 175, 55, 0.15); color: #B8860B;">
                <i class="fa-regular fa-eye"></i>
            </div>
            <div>
                <div class="metric-num">{{ number_format($metrics['total_views']) }}</div>
                <div class="metric-label">మొత్తం వీక్షణలు (Views)</div>
            </div>
        </div>
    </div>

    <!-- Quick Action Banner -->
    <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: linear-gradient(135deg, #0A1D37 0%, #173868 100%); color: #FFFFFF;">
        <div>
            <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 20px; color: #FFFFFF; margin-bottom: 4px;">
                నేటి దినపత్రిక PDF అప్‌లోడ్ చేయడానికి సిద్ధంగా ఉన్నారా?
            </h3>
            <p style="color: #CBD5E1; font-size: 14px;">
                PDF ఫైల్‌ను అప్‌లోడ్ చేసి, పేజీలను సరిచూసి, ఉదయం 5:00 గంటలకు ఆటోమేటిక్ ప్రచురణకు షెడ్యూల్ చేయండి.
            </p>
        </div>
        <div style="display: flex; gap: 12px;">
            @if(auth()->user()->canUpload())
                <a href="{{ route('admin.editions.create') }}" class="btn-admin btn-gold">
                    <i class="fa-solid fa-cloud-arrow-up"></i> కొత్త PDF అప్‌లోడ్ చేయండి
                </a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">
        <!-- Pending Approvals -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-navy);">
                    <i class="fa-solid fa-clipboard-check" style="color: #F59E0B;"></i> ఆమోదం కొరకు వేచిఉన్నవి (Pending Approval)
                </h3>
                <a href="{{ route('admin.editions.index', ['status' => 'pending_approval']) }}" style="font-size: 13px; color: var(--admin-navy); font-weight: 700; text-decoration: none;">అన్నీ చూడండి</a>
            </div>

            @if($pendingEditions->isNotEmpty())
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>సంచిక &amp; ప్రాంతం</th>
                                <th>తేదీ</th>
                                <th>సమర్పించిన వారు</th>
                                <th>చర్య</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingEditions as $pe)
                                <tr>
                                    <td>
                                        <strong>{{ $pe->region->name }}</strong>
                                        <div style="font-size: 12px; color: var(--admin-muted);">{{ $pe->title }}</div>
                                    </td>
                                    <td>{{ $pe->edition_date->format('d M Y') }}</td>
                                    <td>{{ $pe->creator?->name ?? 'ఎడిటర్' }}</td>
                                    <td>
                                        <a href="{{ route('admin.editions.edit', $pe) }}" class="btn-admin btn-gold" style="padding: 4px 10px; font-size: 12px;">
                                            సమీక్షించండి
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 32px 16px; color: var(--admin-muted);">
                    <i class="fa-solid fa-circle-check" style="font-size: 28px; color: #10B981; margin-bottom: 8px;"></i>
                    <p>ప్రస్తుతం ఆమోదం కోసం ఎటువంటి సంచికలు లేవు. అన్నీ పూర్తయ్యాయి!</p>
                </div>
            @endif
        </div>

        <!-- Recent Audit Activity -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-navy);">
                    <i class="fa-solid fa-clock-rotate-left"></i> ఇటీవలి ఎడిటోరియల్ కార్యకలాపాలు (Audit Logs)
                </h3>
            </div>

            @if($recentLogs->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($recentLogs as $log)
                        <div style="display: flex; align-items: flex-start; gap: 12px; padding: 10px 12px; border-radius: 6px; background: #F8FAFC; border: 1px solid var(--admin-border);">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: var(--admin-navy);">
                                {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div style="flex: 1; font-size: 13px;">
                                <div><strong>{{ $log->user?->name ?? 'సిస్టమ్' }}</strong> &bull; <span style="text-transform: capitalize; color: #0284C7; font-weight: 700;">{{ str_replace('_', ' ', $log->action) }}</span></div>
                                <div style="color: #64748B; font-size: 12px;">{{ $log->edition?->title }}: {{ $log->notes }}</div>
                            </div>
                            <div style="font-size: 11px; color: #94A3B8; white-space: nowrap;">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--admin-muted); font-size: 13px;">కార్యకలాపాల వివరాలు నమోదు కాలేదు.</p>
            @endif
        </div>
    </div>

@endsection
