@extends('layouts.admin')

@section('title', 'ఆడిట్ లాగ్స్ - VSP TIMES E-Paper')
@section('header_title', 'సిస్టమ్ ఆడిట్ ట్రయిల్ & కార్యకలాపాలు (Audit Logs)')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--admin-navy);">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--admin-gold); margin-right: 8px;"></i>
                {{ app()->getLocale() === 'en' ? 'Editorial Audit Logs' : 'ఎడిటోరియల్ ఆడిట్ లాగ్స్' }}
                <span style="font-size: 14px; font-weight: 600; color: var(--admin-muted); margin-left: 8px;">(ఎడిటోరియల్ ఆడిట్ లాగ్స్)</span>
            </h2>
            <p style="color: var(--admin-muted); font-size: 13px; margin-top: 4px;">
                {{ app()->getLocale() === 'en' ? 'Complete history of uploads, edits, approvals, rejections, scheduling, and publications.' : 'అప్‌లోడ్, ఎడిట్, ఆమోదం, తిరస్కరణ, షెడ్యూల్ మరియు ప్రచురణల సమగ్ర చరిత్ర.' }}
            </p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ app()->getLocale() === 'en' ? 'Search by user name or notes...' : 'యూజర్ పేరు లేదా నోట్స్‌తో శోధించండి...' }}"
                       style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
            </div>

            <div style="min-width: 180px;">
                <select name="action" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit; background: #FFFFFF;">
                    <option value="">{{ app()->getLocale() === 'en' ? '-- All Actions --' : '-- అన్ని చర్యలు (All Actions) --' }}</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ strtoupper($act) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-admin btn-primary" style="padding: 8px 16px;">
                <i class="fa-solid fa-filter"></i> {{ app()->getLocale() === 'en' ? 'Filter' : 'ఫిల్టర్' }}
            </button>

            @if(request('action') || request('search'))
                <a href="{{ route('admin.audit-logs.index') }}" class="btn-admin btn-outline" style="padding: 8px 16px;">రీసెట్</a>
            @endif
        </form>
    </div>

    <!-- Logs Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #F8FAFC; border-bottom: 2px solid var(--admin-border); color: var(--admin-navy);">
                    <th style="padding: 14px 20px; font-weight: 700;">సమయం (IST)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">సిబ్బంది (Staff)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">చర్య (Action)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">సంచిక (Edition)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">స్థితి మార్పు</th>
                    <th style="padding: 14px 20px; font-weight: 700;">వివరాలు / కారణం</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid var(--admin-border);">
                        <td style="padding: 12px 20px; color: #64748B; font-size: 12px; white-space: nowrap;">
                            <div>{{ $log->created_at->timezone('Asia/Kolkata')->format('d M Y') }}</div>
                            <div style="font-weight: 700; color: var(--admin-navy);">{{ $log->created_at->timezone('Asia/Kolkata')->format('h:i:s A') }}</div>
                        </td>
                        <td style="padding: 12px 20px; font-weight: 700; color: var(--admin-navy);">
                            {{ $log->user->name ?? 'సిస్టమ్ (ఆటోమేటిక్)' }}
                        </td>
                        <td style="padding: 12px 20px;">
                            @php
                                $actionColors = [
                                    'upload' => ['bg' => '#EFF6FF', 'color' => '#1E40AF'],
                                    'submit_approval' => ['bg' => '#FEF3C7', 'color' => '#92400E'],
                                    'approve_and_schedule' => ['bg' => '#ECFDF5', 'color' => '#065F46'],
                                    'approve_and_publish' => ['bg' => '#D1FAE5', 'color' => '#047857'],
                                    'reject' => ['bg' => '#FEE2E2', 'color' => '#991B1B'],
                                    'publish_now' => ['bg' => '#ECFDF5', 'color' => '#065F46'],
                                    'auto_publish' => ['bg' => '#E0E7FF', 'color' => '#3730A3'],
                                    'reorder_pages' => ['bg' => '#F3E8FF', 'color' => '#6B21A8'],
                                    'delete' => ['bg' => '#FEE2E2', 'color' => '#991B1B'],
                                ];
                                $c = $actionColors[$log->action] ?? ['bg' => '#F1F5F9', 'color' => '#475569'];
                            @endphp
                            <span style="background: {{ $c['bg'] }}; color: {{ $c['color'] }}; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 11px; text-transform: uppercase;">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td style="padding: 12px 20px; color: var(--admin-navy);">
                            @if($log->edition)
                                <a href="{{ route('admin.editions.edit', $log->edition) }}" style="color: var(--admin-navy); font-weight: 600; text-decoration: none;">
                                    {{ $log->edition->title }}
                                </a>
                                <div style="font-size: 11px; color: var(--admin-muted);">
                                    {{ $log->edition->region->name_te ?? '' }} &bull; {{ \Carbon\Carbon::parse($log->edition->edition_date)->format('d-m-Y') }}
                                </div>
                            @else
                                <span style="color: #94A3B8;">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px 20px; font-size: 12px; color: #64748B;">
                            @if($log->from_status || $log->to_status)
                                <span>{{ $log->from_status ?: 'start' }}</span>
                                <span style="color: var(--admin-gold); font-weight: 700;">&rarr;</span>
                                <span style="font-weight: 700; color: var(--admin-navy);">{{ $log->to_status }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 12px 20px; color: #475569; font-size: 12px; max-width: 300px;">
                            {{ $log->notes ?: 'ఎటువంటి వివరాలు నమోదు కాలేదు.' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: var(--admin-muted);">
                            ఎటువంటి ఆడిట్ లాగ్ రికార్డులు కనుగొనబడలేదు.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding: 16px 20px; border-top: 1px solid var(--admin-border);">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
