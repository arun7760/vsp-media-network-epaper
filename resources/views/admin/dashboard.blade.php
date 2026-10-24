@extends('layouts.admin')

@section('title', __('admin.dashboard') . ' | VSP TIMES E-Paper CMS')
@section('header_title', __('admin.editorial_dashboard'))

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
        border-radius: 12px;
        padding: 20px 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(10, 29, 55, 0.08);
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .metric-num {
        font-size: 28px;
        font-weight: 800;
        color: var(--admin-navy);
        line-height: 1.1;
    }
    .metric-label {
        font-size: 13px;
        color: var(--admin-muted);
        font-weight: 600;
        margin-top: 3px;
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

    <!-- Metrics Grid (Styled after vspmedianetwork.com/admin/dashboard) -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon" style="background: #E0F2FE; color: #0284C7;">
                <i class="fa-solid fa-newspaper"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['total_editions'] }}</div>
                <div class="metric-label">{{ __('admin.metrics_total') }}</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #DCFCE7; color: #16A34A;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['published_editions'] }}</div>
                <div class="metric-label">{{ __('admin.metrics_live') }}</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #FEF3C7; color: #D97706;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['pending_approvals'] }}</div>
                <div class="metric-label">{{ __('admin.metrics_pending') }}</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #E0E7FF; color: #4F46E5;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <div class="metric-num">{{ $metrics['scheduled_editions'] }}</div>
                <div class="metric-label">{{ __('admin.metrics_scheduled') }}</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background: #F3E8FF; color: #9333EA;">
                <i class="fa-regular fa-eye"></i>
            </div>
            <div>
                <div class="metric-num">{{ number_format($metrics['total_views']) }}</div>
                <div class="metric-label">{{ __('admin.metrics_views') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Action Banner -->
    <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: linear-gradient(135deg, #0A1D37 0%, #173868 100%); color: #FFFFFF; border-radius: 12px; margin-bottom: 28px;">
        <div>
            <h3 style="font-family: inherit; font-size: 20px; font-weight: 800; color: #FFFFFF; margin-bottom: 6px;">
                {{ __('admin.ready_upload_title') }}
            </h3>
            <p style="color: #CBD5E1; font-size: 14px; margin: 0;">
                {{ __('admin.ready_upload_desc') }}
            </p>
        </div>
        <div style="display: flex; gap: 12px;">
            @if(auth()->user()->canUpload())
                <a href="{{ route('admin.editions.create') }}" class="btn-admin btn-gold" style="font-size: 14px; padding: 11px 22px;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('admin.upload_new_pdf') }}
                </a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">
        <!-- Pending Approvals -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-navy); margin: 0;">
                    <i class="fa-solid fa-clipboard-check" style="color: #F59E0B; margin-right: 6px;"></i> {{ __('admin.pending_approval_heading') }}
                </h3>
                <a href="{{ route('admin.editions.index', ['status' => 'pending_approval']) }}" style="font-size: 13px; color: var(--admin-blue); font-weight: 700; text-decoration: none;">
                    {{ __('admin.view_all') }} &rarr;
                </a>
            </div>

            @if($pendingEditions->isNotEmpty())
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.table_edition_region') }}</th>
                                <th>{{ __('admin.table_date') }}</th>
                                <th>{{ __('admin.table_submitted_by') }}</th>
                                <th>{{ __('admin.table_action') }}</th>
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
                                    <td>{{ $pe->creator?->name ?? 'Editor' }}</td>
                                    <td>
                                        <a href="{{ route('admin.editions.edit', $pe) }}" class="btn-admin btn-gold" style="padding: 4px 10px; font-size: 12px;">
                                            {{ __('admin.table_review') }}
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
                    <p style="margin: 0;">{{ __('admin.no_pending_editions') }}</p>
                </div>
            @endif
        </div>

        <!-- Recent Audit Activity -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-navy); margin: 0;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--admin-blue); margin-right: 6px;"></i> {{ __('admin.recent_audit_logs') }}
                </h3>
            </div>

            @if($recentLogs->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($recentLogs as $log)
                        <div style="display: flex; align-items: flex-start; gap: 12px; padding: 10px 12px; border-radius: 6px; background: #F8FAFC; border: 1px solid var(--admin-border);">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #E0F2FE; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #0284C7;">
                                {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div style="flex: 1; font-size: 13px;">
                                <div><strong>{{ $log->user?->name ?? __('admin.system') }}</strong> &bull; <span style="text-transform: capitalize; color: #0284C7; font-weight: 700;">{{ str_replace('_', ' ', $log->action) }}</span></div>
                                <div style="color: #64748B; font-size: 12px;">{{ $log->edition?->title }}: {{ $log->notes }}</div>
                            </div>
                            <div style="font-size: 11px; color: #94A3B8; white-space: nowrap;">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--admin-muted); font-size: 13px; margin: 0;">{{ __('admin.no_recent_activity') }}</p>
            @endif
        </div>
    </div>

@endsection
