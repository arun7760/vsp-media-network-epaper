@extends('layouts.admin')

@section('title', __('admin.dashboard') . ' | VSP TIMES E-Paper CMS')
@section('header_title', __('admin.editorial_dashboard'))

@push('styles')
<style>
    /* Premium Enterprise Dashboard Metrics */
    .metrics-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }

    .premium-stat-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 20px 18px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .premium-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.09);
        border-color: #CBD5E1;
    }

    .stat-accent-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .accent-blue { background: linear-gradient(90deg, #0284C7 0%, #38BDF8 100%); }
    .accent-green { background: linear-gradient(90deg, #059669 0%, #34D399 100%); }
    .accent-amber { background: linear-gradient(90deg, #D97706 0%, #FBBF24 100%); }
    .accent-indigo { background: linear-gradient(90deg, #6366F1 0%, #818CF8 100%); }
    .accent-rose { background: linear-gradient(90deg, #E11D48 0%, #FB7185 100%); }

    .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .icon-blue { background: #E0F2FE; color: #0284C7; }
    .icon-green { background: #ECFDF5; color: #059669; }
    .icon-amber { background: #FFFBEB; color: #D97706; }
    .icon-indigo { background: #EEF2FF; color: #6366F1; }
    .icon-rose { background: #FFF1F2; color: #E11D48; }

    .stat-number {
        font-size: 32px;
        font-weight: 800;
        color: #0F172A;
        line-height: 1;
        letter-spacing: -0.5px;
        font-family: var(--font-sans);
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 12px;
    }

    .stat-footer-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        width: fit-content;
    }
    .badge-soft-blue { background: #F0F9FF; color: #0369A1; border: 1px solid #BAE6FD; }
    .badge-soft-green { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
    .badge-soft-amber { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }
    .badge-soft-indigo { background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; }
    .badge-soft-rose { background: #FFF1F2; color: #BE123C; border: 1px solid #FECDD3; }

    /* Command Publishing Hero Desk */
    .publishing-desk-card {
        background: linear-gradient(135deg, #061226 0%, #0C2145 50%, #15386C 100%);
        border-radius: 16px;
        padding: 32px 36px;
        color: #FFFFFF;
        margin-bottom: 30px;
        box-shadow: 0 12px 36px rgba(6, 18, 38, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.12);
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }
    .publishing-desk-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(217, 27, 36, 0.35) 0%, transparent 70%);
        pointer-events: none;
    }
    .desk-header-tag {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 255, 255, 0.12);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #F8FAFC;
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 12px;
    }
    .desk-heading {
        font-size: 24px;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 8px;
        letter-spacing: -0.3px;
    }
    .desk-subheading {
        font-size: 14px;
        color: #CBD5E1;
        max-width: 620px;
        line-height: 1.6;
        margin: 0;
    }
    .desk-actions {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        z-index: 2;
    }
    .btn-hero-primary {
        background: linear-gradient(135deg, var(--admin-red) 0%, #A90D15 100%);
        color: #FFFFFF !important;
        padding: 13px 26px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        box-shadow: 0 4px 16px rgba(217, 27, 36, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: all 0.2s;
    }
    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(217, 27, 36, 0.6);
        filter: brightness(1.08);
    }
    .btn-hero-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #FFFFFF !important;
        padding: 13px 22px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        transition: all 0.2s;
    }
    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    /* Grid Section: Approvals & Audit Timeline */
    .dashboard-grid-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
    }

    .panel-card {
        background: #FFFFFF;
        border-radius: 14px;
        border: 1px solid #E2E8F0;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
    }
    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 16px;
        margin-bottom: 18px;
        border-bottom: 1px solid #F1F5F9;
    }
    .panel-title {
        font-size: 16px;
        font-weight: 800;
        color: #0F172A;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .panel-link {
        font-size: 13px;
        font-weight: 700;
        color: var(--admin-blue);
        text-decoration: none;
        transition: color 0.15s;
    }
    .panel-link:hover {
        color: var(--admin-red);
    }

    /* Empty state */
    .empty-card-state {
        padding: 44px 20px;
        text-align: center;
        color: #64748B;
        margin: auto;
    }
    .empty-icon-circle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #ECFDF5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 14px;
        border: 2px solid #A7F3D0;
    }

    /* Timeline Audit List */
    .timeline-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .timeline-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 14px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .timeline-item:hover {
        background: #FFFFFF;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        border-color: #CBD5E1;
    }
    .timeline-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);
        color: #FFFFFF;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    }
    .timeline-content {
        flex: 1;
        font-size: 13px;
    }
    .timeline-user {
        font-weight: 700;
        color: #0F172A;
    }
    .timeline-action-badge {
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 2px 7px;
        border-radius: 4px;
        background: #E0F2FE;
        color: #0369A1;
        margin-left: 6px;
    }
    .timeline-notes {
        font-size: 12px;
        color: #64748B;
        margin-top: 2px;
        line-height: 1.4;
    }
    .timeline-time {
        font-size: 11px;
        color: #94A3B8;
        font-weight: 600;
        white-space: nowrap;
    }

    @media (max-width: 1200px) {
        .metrics-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 900px) {
        .metrics-container {
            grid-template-columns: 1fr 1fr;
        }
        .dashboard-grid-2col {
            grid-template-columns: 1fr;
        }
        .publishing-desk-card {
            padding: 24px;
        }
    }
    @media (max-width: 600px) {
        .metrics-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

    <!-- 1. Five Elevated Floating Stat Cards -->
    <div class="metrics-container">
        <!-- Total Editions -->
        <div class="premium-stat-card">
            <div class="stat-accent-bar accent-blue"></div>
            <div>
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('admin.metrics_total') }}</span>
                    <div class="stat-icon-wrap icon-blue">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                </div>
                <div class="stat-number">{{ $metrics['total_editions'] }}</div>
            </div>
            <div class="stat-footer-badge badge-soft-blue">
                <i class="fa-solid fa-layer-group"></i> {{ __('admin.badge_all_broadsheets') }}
            </div>
        </div>

        <!-- Live Editions -->
        <div class="premium-stat-card">
            <div class="stat-accent-bar accent-green"></div>
            <div>
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('admin.metrics_live') }}</span>
                    <div class="stat-icon-wrap icon-green">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="stat-number">{{ $metrics['published_editions'] }}</div>
            </div>
            <div class="stat-footer-badge badge-soft-green">
                <i class="fa-solid fa-circle-dot"></i> {{ __('admin.badge_live_readers') }}
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="premium-stat-card">
            <div class="stat-accent-bar accent-amber"></div>
            <div>
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('admin.metrics_pending') }}</span>
                    <div class="stat-icon-wrap icon-amber">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div class="stat-number">{{ $metrics['pending_approvals'] }}</div>
            </div>
            <div class="stat-footer-badge badge-soft-amber">
                <i class="fa-solid fa-user-check"></i> {{ __('admin.badge_requires_review') }}
            </div>
        </div>

        <!-- Scheduled Editions -->
        <div class="premium-stat-card">
            <div class="stat-accent-bar accent-indigo"></div>
            <div>
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('admin.metrics_scheduled') }}</span>
                    <div class="stat-icon-wrap icon-indigo">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-number">{{ $metrics['scheduled_editions'] }}</div>
            </div>
            <div class="stat-footer-badge badge-soft-indigo">
                <i class="fa-solid fa-robot"></i> {{ __('admin.badge_cron_release') }}
            </div>
        </div>

        <!-- Total Readership Views -->
        <div class="premium-stat-card">
            <div class="stat-accent-bar accent-rose"></div>
            <div>
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('admin.metrics_views') }}</span>
                    <div class="stat-icon-wrap icon-rose">
                        <i class="fa-regular fa-eye"></i>
                    </div>
                </div>
                <div class="stat-number">{{ number_format($metrics['total_views']) }}</div>
            </div>
            <div class="stat-footer-badge badge-soft-rose">
                <i class="fa-solid fa-chart-line"></i> {{ __('admin.badge_realtime_views') }}
            </div>
        </div>
    </div>

    <!-- 2. Command Publishing Hero Desk -->
    <div class="publishing-desk-card">
        <div>
            <div class="desk-header-tag">
                <i class="fa-solid fa-newspaper" style="color: var(--admin-red);"></i>
                {{ __('admin.publishing_desk_badge') }}
            </div>
            <h3 class="desk-heading">
                {{ __('admin.ready_upload_title') }}
            </h3>
            <p class="desk-subheading">
                {{ __('admin.ready_upload_desc') }}
            </p>
        </div>

        <div class="desk-actions">
            @if(auth()->user()->canUpload())
                <a href="{{ route('admin.editions.create') }}" class="btn-hero-primary">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('admin.upload_new_pdf') }}</span>
                </a>
            @endif
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.analytics.index') }}" class="btn-hero-secondary">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>{{ __('admin.analytics') }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 3. Two-Column Panels: Pending Approvals & Editorial Activity -->
    <div class="dashboard-grid-2col">
        <!-- Pending Approvals Queue -->
        <div class="panel-card">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="fa-solid fa-clipboard-check" style="color: #D97706;"></i>
                    <span>{{ __('admin.pending_approval_heading') }}</span>
                </h3>
                <a href="{{ route('admin.editions.index', ['status' => 'pending_approval']) }}" class="panel-link">
                    {{ __('admin.view_all') }} &rarr;
                </a>
            </div>

            @if($pendingEditions->isNotEmpty())
                <div style="overflow-x: auto;">
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
                                        <strong>{{ $pe->region->display_name }}</strong>
                                        <div style="font-size: 12px; color: var(--admin-muted);">{{ $pe->display_title }}</div>
                                    </td>
                                    <td>{{ $pe->edition_date->format('d M Y') }}</td>
                                    <td>{{ $pe->creator?->name ?? 'Editor' }}</td>
                                    <td>
                                        <a href="{{ route('admin.editions.edit', $pe) }}" class="btn-admin btn-gold" style="padding: 5px 12px; font-size: 12px; border-radius: 6px;">
                                            <i class="fa-solid fa-magnifying-glass"></i> {{ __('admin.table_review') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-card-state">
                    <div class="empty-icon-circle">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h4 style="font-size: 15px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">
                        {{ __('admin.all_editions_approved') }}
                    </h4>
                    <p style="font-size: 13px; color: #64748B; margin: 0;">
                        {{ __('admin.no_pending_editions') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Recent Editorial Activity (Audit Logs) -->
        <div class="panel-card">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--admin-blue);"></i>
                    <span>{{ __('admin.recent_audit_logs') }}</span>
                </h3>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.audit-logs.index') }}" class="panel-link">
                        {{ __('admin.view_all') }} &rarr;
                    </a>
                @endif
            </div>

            @if($recentLogs->isNotEmpty())
                <div class="timeline-list">
                    @foreach($recentLogs as $log)
                        <div class="timeline-item">
                            <div class="timeline-avatar">
                                {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="timeline-content">
                                <div>
                                    <span class="timeline-user">{{ $log->user?->name ?? __('admin.system') }}</span>
                                    <span class="timeline-action-badge">{{ str_replace('_', ' ', $log->action) }}</span>
                                </div>
                                <div class="timeline-notes">
                                    <strong>{{ $log->edition?->title ?? 'Edition' }}</strong>: {{ $log->notes }}
                                </div>
                            </div>
                            <div class="timeline-time">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--admin-muted); font-size: 13px; margin: 0; text-align: center; padding: 40px 0;">
                    {{ __('admin.no_recent_activity') }}
                </p>
            @endif
        </div>
    </div>

@endsection
