@extends('layouts.admin')

@section('title', __('admin.manage_editions') . ' | VSP TIMES E-Paper CMS')
@section('header_title', __('admin.manage_editions'))

@push('styles')
<style>
    .filter-strip {
        background: #FFFFFF;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .search-filter-portal {
        background: #FFFFFF;
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(10, 29, 55, 0.04);
    }
    .portal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--admin-border);
    }
    .portal-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--admin-navy);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .portal-form-grid {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.2fr 1.2fr auto;
        gap: 12px;
        align-items: end;
    }
    .filter-input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .filter-input-group label {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--admin-navy);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .filter-control {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        font-size: 13.5px;
        color: var(--admin-navy);
        background: #FFFFFF;
        outline: none;
        transition: all 0.2s;
    }
    .filter-control:focus {
        border-color: var(--admin-navy);
        box-shadow: 0 0 0 3px rgba(10, 29, 55, 0.1);
    }
    .status-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 7px;
        border-radius: 999px;
        font-size: 11px;
        margin-left: 6px;
        background: rgba(0,0,0,0.08);
    }
    .status-filter-btn.active .status-count-badge {
        background: rgba(255,255,255,0.25);
        color: #FFFFFF;
    }
    @media (max-width: 1024px) {
        .portal-form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 640px) {
        .portal-form-grid {
            grid-template-columns: 1fr;
        }
    }

    .status-filters {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .status-filter-btn {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        color: var(--admin-muted);
        background: #F1F5F9;
        transition: all 0.15s;
    }
    .status-filter-btn:hover {
        background: #E2E8F0;
        color: var(--admin-navy);
    }
    .status-filter-btn.active {
        background: var(--admin-navy);
        color: #FFFFFF;
    }

    .table-container {
        background: #FFFFFF;
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .editions-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        text-align: left;
    }
    .editions-table th {
        background: #F8FAFC;
        padding: 14px 18px;
        font-weight: 700;
        color: var(--admin-navy);
        border-bottom: 2px solid var(--admin-border);
    }
    .editions-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--admin-border);
        vertical-align: middle;
    }
    .thumb-mini {
        width: 48px;
        height: 64px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid var(--admin-border);
        background: #EEF2F6;
        display: block;
    }
    .badge-status {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .badge-published { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
    .badge-scheduled { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
    .badge-pending_approval { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
    .badge-draft { background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; }

    /* Delete Confirmation Modal */
    .delete-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(10, 29, 55, 0.65);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeInModal 0.2s ease-out;
    }
    .delete-modal-card {
        background: #FFFFFF;
        border-radius: 14px;
        width: 100%;
        max-width: 480px;
        padding: 30px;
        box-shadow: 0 20px 45px rgba(0,0,0,0.25);
        text-align: center;
        animation: scaleUpModal 0.25s ease-out;
    }
    @keyframes fadeInModal {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleUpModal {
        from { transform: scale(0.92); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .delete-modal-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #FEE2E2;
        color: #DC2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto 16px;
        box-shadow: 0 0 0 8px #FEF2F2;
    }
    .delete-modal-title {
        font-family: 'Ramabhadra', sans-serif;
        font-size: 20px;
        color: var(--admin-navy);
        margin-bottom: 2px;
    }
    .delete-modal-subtitle {
        font-size: 12px;
        color: var(--admin-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
        font-weight: 700;
    }
    .delete-modal-desc {
        font-size: 13.5px;
        color: #475569;
        line-height: 1.5;
        margin-bottom: 18px;
    }
    .delete-modal-info {
        background: #F8FAFC;
        border: 1px solid var(--admin-border);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 22px;
        text-align: left;
    }
    .delete-modal-actions {
        display: flex;
        gap: 12px;
    }
</style>
@endpush

@section('content')

    <!-- Editions Search & Filter Portal -->
    <div class="search-filter-portal">
        <div class="portal-header">
            <div class="portal-title">
                <i class="fa-solid fa-sliders" style="color: var(--admin-red);"></i>
                <span>{{ app()->getLocale() === 'en' ? 'Editions Search & Filter Portal' : 'సంచికల శోధన & ఫిల్టర్ పోర్టల్' }}</span>
            </div>
            
            @if(auth()->user()->canUpload())
                <a href="{{ route('admin.editions.create') }}" class="btn-admin btn-gold">
                    <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('admin.upload_edition') }}
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.editions.index') }}">
            <div class="portal-form-grid">
                <!-- Search Keyword -->
                <div class="filter-input-group">
                    <label>
                        <i class="fa-solid fa-magnifying-glass" style="margin-right: 4px; color: var(--admin-muted);"></i>
                        {{ app()->getLocale() === 'en' ? 'Search Keyword' : 'సంచిక శోధన' }}
                    </label>
                    <div style="position: relative;">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="{{ app()->getLocale() === 'en' ? 'Title, code, volume, issue...' : 'శీర్షిక, కోడ్, సంపుటి శోధించండి...' }}" 
                               class="filter-control" style="padding-left: 36px;">
                        <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 13px;"></i>
                    </div>
                </div>

                <!-- Region Selector -->
                <div class="filter-input-group">
                    <label>
                        <i class="fa-solid fa-map-location-dot" style="margin-right: 4px; color: var(--admin-muted);"></i>
                        {{ app()->getLocale() === 'en' ? 'Region / Edition' : 'ప్రాంతం / ఎడిషన్' }}
                    </label>
                    <select name="region_id" class="filter-control">
                        <option value="">{{ app()->getLocale() === 'en' ? '-- All Regions --' : '-- అన్ని ప్రాంతాలు --' }}</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" {{ request('region_id') == $reg->id ? 'selected' : '' }}>
                                {{ $reg->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Selector -->
                <div class="filter-input-group">
                    <label>
                        <i class="fa-solid fa-tag" style="margin-right: 4px; color: var(--admin-muted);"></i>
                        {{ app()->getLocale() === 'en' ? 'Status' : 'ప్రచురణ స్థితి' }}
                    </label>
                    <select name="status" class="filter-control">
                        <option value="">{{ app()->getLocale() === 'en' ? '-- All Statuses --' : '-- అన్ని స్థితులు --' }}</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Published (Live)' : 'లైవ్ (Published)' }}</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Scheduled (5:00 AM)' : 'షెడ్యూల్డ్ (5:00 AM)' }}</option>
                        <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Pending Approval' : 'ఆమోదం కోసం (Pending)' }}</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Drafts' : 'డ్రాఫ్ట్ (Drafts)' }}</option>
                    </select>
                </div>

                <!-- Date Picker -->
                <div class="filter-input-group">
                    <label>
                        <i class="fa-regular fa-calendar-days" style="margin-right: 4px; color: var(--admin-muted);"></i>
                        {{ app()->getLocale() === 'en' ? 'Edition Date' : 'సంచిక తేదీ' }}
                    </label>
                    <input type="date" name="date" value="{{ request('date') }}" class="filter-control">
                </div>

                <!-- Filter Actions -->
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-admin btn-navy" style="height: 39px; padding: 0 16px;">
                        <i class="fa-solid fa-filter"></i> {{ app()->getLocale() === 'en' ? 'Filter' : 'ఫిల్టర్' }}
                    </button>
                    @if(request('search') || request('region_id') || request('status') || request('date'))
                        <a href="{{ route('admin.editions.index') }}" class="btn-admin btn-outline" style="height: 39px; padding: 0 14px;" title="{{ app()->getLocale() === 'en' ? 'Reset Filters' : 'రీసెట్ చేయండి' }}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Quick Status Filter Pills with Counts -->
            <div class="status-filters" style="margin-top: 16px; padding-top: 14px; border-top: 1px dashed var(--admin-border);">
                <a href="{{ route('admin.editions.index', array_merge(request()->except('status', 'page'))) }}" 
                   class="status-filter-btn {{ !request('status') ? 'active' : '' }}">
                    {{ __('admin.filter_all') }}
                    <span class="status-count-badge">{{ $statusCounts['all'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.editions.index', array_merge(request()->except('page'), ['status' => 'pending_approval'])) }}" 
                   class="status-filter-btn {{ request('status') === 'pending_approval' ? 'active' : '' }}">
                    {{ __('admin.filter_pending') }}
                    <span class="status-count-badge">{{ $statusCounts['pending_approval'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.editions.index', array_merge(request()->except('page'), ['status' => 'scheduled'])) }}" 
                   class="status-filter-btn {{ request('status') === 'scheduled' ? 'active' : '' }}">
                    {{ __('admin.filter_scheduled') }}
                    <span class="status-count-badge">{{ $statusCounts['scheduled'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.editions.index', array_merge(request()->except('page'), ['status' => 'published'])) }}" 
                   class="status-filter-btn {{ request('status') === 'published' ? 'active' : '' }}">
                    {{ __('admin.filter_published') }}
                    <span class="status-count-badge">{{ $statusCounts['published'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.editions.index', array_merge(request()->except('page'), ['status' => 'draft'])) }}" 
                   class="status-filter-btn {{ request('status') === 'draft' ? 'active' : '' }}">
                    {{ __('admin.filter_draft') }}
                    <span class="status-count-badge">{{ $statusCounts['draft'] ?? 0 }}</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Editions Table -->
    <div class="table-container">
        @if($editions->isNotEmpty())
            <div style="overflow-x: auto;">
                <table class="editions-table" data-datatable="true" data-page-size="10">
                    <thead>
                        <tr>
                            <th style="width: 70px;" class="no-sort">{{ __('admin.col_cover') }}</th>
                            <th>{{ __('admin.col_title_details') }}</th>
                            <th>{{ __('admin.col_region') }}</th>
                            <th>{{ __('admin.col_date') }}</th>
                            <th>{{ __('admin.col_pages') }}</th>
                            <th>{{ __('admin.col_status') }}</th>
                            <th>{{ __('admin.col_views') }}</th>
                            <th style="text-align: right;" class="no-sort">{{ __('admin.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($editions as $ed)
                            <tr>
                                <td>
                                    <img src="{{ $ed->cover_url }}" alt="{{ $ed->title }}" class="thumb-mini" onerror="this.src='/images/logo.png';">
                                </td>
                                <td>
                                    <strong style="color: var(--admin-navy); font-size: 15px;">{{ $ed->display_title }}</strong>
                                    <div style="font-size: 12px; color: var(--admin-muted);">
                                        {{ app()->getLocale() === 'en' ? str_replace(['సంపుటి', 'సంచిక'], ['Vol', 'Issue'], $ed->volume_number . ' • ' . $ed->issue_number) : $ed->volume_number . ' • ' . $ed->issue_number }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #0284C7;">{{ $ed->region->display_name }}</span>
                                </td>
                                <td>{{ $ed->edition_date->format('d M Y') }}</td>
                                <td><strong>{{ $ed->total_pages }}</strong> {{ __('admin.pages') }}</td>
                                <td>
                                    <span class="badge-status badge-{{ $ed->status }}">
                                        {{ str_replace('_', ' ', $ed->status) }}
                                    </span>
                                </td>
                                <td>{{ number_format($ed->views_count) }}</td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('admin.editions.edit', $ed) }}" class="btn-admin btn-outline" style="padding: 5px 12px; font-size: 12px;" title="{{ __('admin.edit') }}">
                                        <i class="fa-solid fa-pen-to-square"></i> {{ __('admin.editor') }}
                                    </a>

                                    @if(!$ed->isPublished() && auth()->user()->canPublish())
                                        <form action="{{ route('admin.editions.publish', $ed) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn-admin btn-outline" style="padding: 5px 10px; font-size: 12px; color: #10B981; border-color: #A7F3D0; background: #ECFDF5;" title="{{ app()->getLocale() === 'en' ? 'Publish Live' : 'లైవ్ చేయండి' }}">
                                                <i class="fa-solid fa-bolt"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($ed->isPublished())
                                        <a href="{{ route('epaper.reader', $ed) }}" target="_blank" class="btn-admin btn-outline" style="padding: 5px 10px; font-size: 12px; color: #10B981;" title="{{ __('admin.view') }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->canDelete())
                                        <button type="button" 
                                                class="btn-admin btn-outline" 
                                                style="padding: 5px 10px; font-size: 12px; color: #DC2626;" 
                                                title="{{ __('admin.delete') }}"
                                                onclick="openDeleteEditionModal('{{ route('admin.editions.destroy', $ed) }}', '{{ addslashes($ed->title) }}', '{{ $ed->edition_date->format('d M Y') }}', '{{ addslashes($ed->region->name) }}')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $editions->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--admin-muted);">
                <i class="fa-regular fa-folder-open" style="font-size: 40px; margin-bottom: 12px; color: #94A3B8;"></i>
                <h3 style="font-size: 18px; color: var(--admin-navy); margin-bottom: 4px;">
                    {{ app()->getLocale() === 'en' ? 'No Editions Found' : 'ఎటువంటి సంచికలు కనుగొనబడలేదు' }}
                </h3>
                <p style="margin-bottom: 16px;">
                    {{ app()->getLocale() === 'en' ? 'No editions match your selected search or filter criteria.' : 'ఎంచుకున్న శోధన లేదా ఫిల్టర్‌కు అనుగుణంగా రికార్డులు ఏవీ లేవు.' }}
                </p>
                @if(request('search') || request('region_id') || request('status') || request('date'))
                    <a href="{{ route('admin.editions.index') }}" class="btn-admin btn-navy" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-rotate-left"></i> {{ app()->getLocale() === 'en' ? 'Reset All Filters' : 'అన్ని ఫిల్టర్‌లను రీసెట్ చేయండి' }}
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal Popup -->
    <div id="deleteEditionModal" class="delete-modal-backdrop" style="display: none;">
        <div class="delete-modal-card">
            <div class="delete-modal-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="delete-modal-title">సంచికను తొలగించాలా?</h3>
            <p class="delete-modal-subtitle">Delete Edition Confirmation</p>
            
            <p class="delete-modal-desc">
                మీరు నిజంగానే ఈ సంచికను తొలగించాలనుకుంటున్నారా? ఈ చర్య ద్వారా సంచిక పబ్లిక్ పోర్టల్ మరియు ఆర్కైవ్ నుండి తక్షణమే తీసివేయబడుతుంది.
            </p>

            <div class="delete-modal-info">
                <div style="font-weight: 700; color: var(--admin-navy); font-size: 14px; margin-bottom: 4px;" id="modalEditionTitle"></div>
                <div style="font-size: 12px; color: var(--admin-muted);" id="modalEditionMeta"></div>
            </div>

            <form id="deleteEditionForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="delete-modal-actions">
                    <button type="button" class="btn-admin btn-outline" style="flex: 1; justify-content: center; padding: 10px; font-weight: 700;" onclick="closeDeleteEditionModal()">
                        <i class="fa-solid fa-xmark"></i> రద్దు చేయండి (Cancel)
                    </button>
                    <button type="submit" class="btn-admin btn-danger" style="flex: 1; justify-content: center; padding: 10px; font-weight: 700;">
                        <i class="fa-solid fa-trash-can"></i> అవును, తొలగించండి (Delete)
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openDeleteEditionModal(url, title, date, region) {
        document.getElementById('deleteEditionForm').action = url;
        document.getElementById('modalEditionTitle').textContent = title;
        document.getElementById('modalEditionMeta').textContent = region + ' • ' + date;
        const modal = document.getElementById('deleteEditionModal');
        modal.style.display = 'flex';
    }

    function closeDeleteEditionModal() {
        const modal = document.getElementById('deleteEditionModal');
        modal.style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('deleteEditionModal');
        if (e.target === modal) {
            closeDeleteEditionModal();
        }
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteEditionModal();
        }
    });
</script>
@endpush
