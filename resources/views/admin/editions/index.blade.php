@extends('layouts.admin')

@section('title', 'సంచికల నిర్వహణ | VSP TIMES E-Paper CMS')
@section('header_title', 'ఈ-పేపర్ సంచికల నిర్వహణ (Manage Editions)')

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

    <!-- Top Action & Filter Strip -->
    <div class="filter-strip">
        <div class="status-filters">
            <a href="{{ route('admin.editions.index') }}" class="status-filter-btn {{ !request('status') ? 'active' : '' }}">అన్నీ (All)</a>
            <a href="{{ route('admin.editions.index', ['status' => 'pending_approval']) }}" class="status-filter-btn {{ request('status') === 'pending_approval' ? 'active' : '' }}">ఆమోదం కోసం (Pending)</a>
            <a href="{{ route('admin.editions.index', ['status' => 'scheduled']) }}" class="status-filter-btn {{ request('status') === 'scheduled' ? 'active' : '' }}">షెడ్యూల్డ్ (5:00 AM)</a>
            <a href="{{ route('admin.editions.index', ['status' => 'published']) }}" class="status-filter-btn {{ request('status') === 'published' ? 'active' : '' }}">ప్రచురితమైనవి (Live)</a>
            <a href="{{ route('admin.editions.index', ['status' => 'draft']) }}" class="status-filter-btn {{ request('status') === 'draft' ? 'active' : '' }}">డ్రాఫ్ట్‌లు (Drafts)</a>
        </div>

        @if(auth()->user()->canUpload())
            <a href="{{ route('admin.editions.create') }}" class="btn-admin btn-gold">
                <i class="fa-solid fa-cloud-arrow-up"></i> కొత్త సంచిక అప్‌లోడ్
            </a>
        @endif
    </div>

    <!-- Editions Table -->
    <div class="table-container">
        @if($editions->isNotEmpty())
            <div style="overflow-x: auto;">
                <table class="editions-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">కవర్</th>
                            <th>సంచిక శీర్షిక &amp; వివరాలు</th>
                            <th>ప్రాంతం</th>
                            <th>తేదీ</th>
                            <th>పేజీలు</th>
                            <th>స్థితి (Status)</th>
                            <th>వీక్షణలు</th>
                            <th style="text-align: right;">చర్యలు</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($editions as $ed)
                            <tr>
                                <td>
                                    <img src="{{ $ed->cover_url }}" alt="{{ $ed->title }}" class="thumb-mini" onerror="this.src='https://placehold.co/48x64/0A1D37/FFFFFF/png?text=VSP';">
                                </td>
                                <td>
                                    <strong style="color: var(--admin-navy); font-size: 15px;">{{ $ed->title }}</strong>
                                    <div style="font-size: 12px; color: var(--admin-muted);">
                                        {{ $ed->volume_number }} &bull; {{ $ed->issue_number }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #0284C7;">{{ $ed->region->name }}</span>
                                </td>
                                <td>{{ $ed->edition_date->format('d M Y') }}</td>
                                <td><strong>{{ $ed->total_pages }}</strong> పేజీలు</td>
                                <td>
                                    <span class="badge-status badge-{{ $ed->status }}">
                                        {{ str_replace('_', ' ', $ed->status) }}
                                    </span>
                                </td>
                                <td>{{ number_format($ed->views_count) }}</td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('admin.editions.edit', $ed) }}" class="btn-admin btn-outline" style="padding: 5px 12px; font-size: 12px;" title="సవరించండి / సమీక్షించండి">
                                        <i class="fa-solid fa-pen-to-square"></i> ఎడిటర్
                                    </a>

                                    @if($ed->isPublished())
                                        <a href="{{ route('epaper.reader', $ed) }}" target="_blank" class="btn-admin btn-outline" style="padding: 5px 10px; font-size: 12px; color: #10B981;" title="లైవ్ రీడర్ చూడండి">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->canDelete())
                                        <button type="button" 
                                                class="btn-admin btn-outline" 
                                                style="padding: 5px 10px; font-size: 12px; color: #DC2626;" 
                                                title="తొలగించండి (Delete)"
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
                <h3 style="font-size: 18px; color: var(--admin-navy); margin-bottom: 4px;">ఎటువంటి సంచికలు కనుగొనబడలేదు</h3>
                <p>ఎంచుకున్న ఫిల్టర్‌కు అనుగుణంగా రికార్డులు ఏవీ లేవు.</p>
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
