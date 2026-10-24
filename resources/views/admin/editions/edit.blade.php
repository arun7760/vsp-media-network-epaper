@extends('layouts.admin')

@section('title', (app()->getLocale() === 'en' ? 'Edition Editor - ' : 'సంచిక ఎడిటర్ - ') . $edition->display_title)
@section('header_title', app()->getLocale() === 'en' ? 'Edition Editor & Management' : 'సంచిక నిర్వహణ & ఎడిటర్ (Edition Editor)')

@section('content')
<div>
    <!-- Top Action Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ route('admin.editions.index') }}" class="btn-admin btn-outline">
                <i class="fa-solid fa-arrow-left"></i> {{ __('admin.editions_list') }}
            </a>
            <div>
                <h2 style="font-family: 'Ramabhadra', sans-serif; font-size: 22px; color: var(--admin-navy); margin-bottom: 2px;">
                    {{ $edition->display_title }}
                </h2>
                <div style="font-size: 13px; color: var(--admin-muted); display: flex; align-items: center; gap: 12px;">
                    <span><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($edition->edition_date)->format('d M Y') }}</span>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $edition->region->display_name }}</span>
                    <span><i class="fa-regular fa-file"></i> {{ $edition->total_pages }} {{ __('admin.pages') }}</span>
                    <span>
                        @if($edition->status === 'published')
                            <span style="background: #ECFDF5; color: #065F46; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-circle-check"></i> {{ __('admin.status_live') }}
                            </span>
                        @elseif($edition->status === 'scheduled')
                            <span style="background: #EFF6FF; color: #1E40AF; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-clock"></i> {{ __('admin.status_scheduled') }} ({{ $edition->scheduled_at ? \Carbon\Carbon::parse($edition->scheduled_at)->format('d M, h:i A') : '5:00 AM' }})
                            </span>
                        @elseif($edition->status === 'pending_approval')
                            <span style="background: #FFFBEB; color: #92400E; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-hourglass-half"></i> {{ __('admin.status_pending') }}
                            </span>
                        @else
                            <span style="background: #F1F5F9; color: #475569; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-pencil"></i> {{ __('admin.status_draft') }}
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Workflow Action Buttons -->
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- Public Preview / View in Reader -->
            @if($edition->status === 'published')
                <a href="{{ route('epaper.edition', $edition) }}" target="_blank" class="btn-admin btn-outline" style="border-color: #10B981; color: #065F46; background: #ECFDF5; font-weight: 700;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('admin.view_in_reader') }}
                </a>
            @else
                <a href="{{ route('epaper.edition', $edition) }}" target="_blank" class="btn-admin btn-outline" title="{{ __('admin.admin_preview') }}" style="font-weight: 700;">
                    <i class="fa-solid fa-eye" style="color: var(--admin-blue);"></i> {{ __('admin.preview') }}
                </a>
            @endif

            <!-- State: DRAFT -->
            @if($edition->status === 'draft')
                <!-- Submit for Editorial Review -->
                @if(auth()->user()->canUpload())
                    <form action="{{ route('admin.editions.submit', $edition) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-admin btn-outline" style="border-color: #F59E0B; color: #B45309; background: #FFFBEB;">
                            <i class="fa-solid fa-paper-plane"></i> {{ __('admin.submit_for_review') }}
                        </button>
                    </form>
                @endif

                <!-- If Admin / SuperAdmin: 1-Click Publish or Schedule directly -->
                @if(auth()->user()->canPublish())
                    <!-- Schedule for 5:00 AM IST -->
                    <form action="{{ route('admin.editions.approve', $edition) }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="publish_action" value="schedule_morning">
                        <button type="submit" class="btn-admin btn-primary">
                            <i class="fa-solid fa-clock"></i> {{ __('admin.approve_and_schedule') }}
                        </button>
                    </form>

                    <!-- Publish Live Immediately -->
                    <form action="{{ route('admin.editions.publish', $edition) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-admin btn-success" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #FFFFFF;">
                            <i class="fa-solid fa-bolt"></i> {{ __('admin.publish_now') }}
                        </button>
                    </form>
                @endif
            @endif

            <!-- State: PENDING APPROVAL -->
            @if($edition->status === 'pending_approval' && auth()->user()->canApprove())
                <!-- Approve & Schedule Morning 5:00 AM -->
                <form action="{{ route('admin.editions.approve', $edition) }}" method="POST" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="publish_action" value="schedule_morning">
                    <button type="submit" class="btn-admin btn-primary">
                        <i class="fa-solid fa-clock"></i> {{ __('admin.approve_and_schedule') }}
                    </button>
                </form>

                <!-- Immediate Publish (Admin/SuperAdmin) -->
                @if(auth()->user()->canPublish())
                    <form action="{{ route('admin.editions.publish', $edition) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-admin btn-success" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #FFFFFF;">
                            <i class="fa-solid fa-bolt"></i> {{ __('admin.publish_now') }}
                        </button>
                    </form>
                @endif

                <!-- Reject Button (Opens Modal with reason) -->
                <button type="button" class="btn-admin btn-danger" onclick="document.getElementById('rejectModal').style.display='flex';" style="background: #EF4444; color: #FFFFFF;">
                    <i class="fa-solid fa-ban"></i> {{ __('admin.reject') }}
                </button>
            @endif

            <!-- State: SCHEDULED -->
            @if($edition->status === 'scheduled' && auth()->user()->canPublish())
                <!-- Publish Live Immediately -->
                <form action="{{ route('admin.editions.publish', $edition) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-admin btn-success" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #FFFFFF;">
                        <i class="fa-solid fa-bolt"></i> {{ __('admin.publish_now') }}
                    </button>
                </form>

                <!-- Revert back to Draft -->
                <form action="{{ route('admin.editions.unpublish', $edition) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-admin btn-outline" style="border-color: #CBD5E1; color: #475569;">
                        <i class="fa-solid fa-arrow-rotate-left"></i> {{ app()->getLocale() === 'en' ? 'Revert to Draft' : 'డ్రాఫ్ట్‌గా మార్చండి' }}
                    </button>
                </form>
            @endif

            <!-- State: PUBLISHED -->
            @if($edition->status === 'published' && auth()->user()->canPublish())
                <!-- Unpublish / Take Down to Draft -->
                <form action="{{ route('admin.editions.unpublish', $edition) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-admin btn-outline" style="border-color: #FCA5A5; color: #DC2626;">
                        <i class="fa-solid fa-eye-slash"></i> {{ app()->getLocale() === 'en' ? 'Unpublish (Move to Draft)' : 'అన్‌పబ్లిష్ చేయండి (డ్రాఫ్ట్‌గా మార్చండి)' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Main Grid: Page Manager & Metadata Sidebar -->
    <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 24px; align-items: start;">
        
        <!-- Left: Broadsheet Page Grid & Reordering -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--admin-border); padding-bottom: 14px;">
                <div>
                    <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 18px; color: var(--admin-navy);">
                        <i class="fa-solid fa-file-invoice" style="color: var(--admin-gold); margin-right: 6px;"></i>
                        {{ __('admin.page_gallery') }}
                    </h3>
                    <p style="font-size: 13px; color: var(--admin-muted); margin-top: 2px;">
                        {{ __('admin.total_pages_available', ['count' => $edition->pages->count()]) }}
                    </p>
                </div>
                <div style="font-size: 12px; background: #F1F5F9; color: var(--admin-muted); padding: 5px 10px; border-radius: 6px; font-weight: 700;">
                    {{ __('admin.cover_page') }}
                </div>
            </div>

            <!-- Pages Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                @foreach($edition->pages as $page)
                    <div style="border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden; background: #FFFFFF; display: flex; flex-direction: column; transition: transform 0.15s, box-shadow 0.15s;"
                         onmouseover="this.style.boxShadow='0 8px 20px rgba(0,0,0,0.08)';"
                         onmouseout="this.style.boxShadow='none';">
                        
                        <!-- Page Preview Thumbnail -->
                        <div style="position: relative; background: #F8FAFC; aspect-ratio: 0.7; overflow: hidden; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--admin-border);">
                            <canvas class="admin-page-thumb-canvas" data-page-num="{{ $page->page_number }}" style="display: none; width: 100%; height: 100%; object-fit: contain;"></canvas>
                            <img src="{{ $page->thumbnail_url }}" alt="{{ __('admin.page') }} {{ $page->page_number }}" 
                                 onerror="this.src='/images/logo.png';"
                                 style="width: 100%; height: 100%; object-fit: contain;">
                            
                            <!-- Page Number Badge -->
                            <div style="position: absolute; top: 8px; left: 8px; background: var(--admin-navy); color: #FFFFFF; font-size: 12px; font-weight: 800; padding: 3px 8px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                {{ __('admin.page') }} {{ $page->page_number }}
                            </div>

                            @if($page->page_number === 1)
                                <div style="position: absolute; top: 8px; right: 8px; background: var(--admin-gold); color: var(--admin-navy); font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 3px; text-transform: uppercase;">
                                    {{ __('admin.cover') }}
                                </div>
                            @endif
                        </div>

                        <!-- Card Controls -->
                        <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px;">
                            <div style="font-size: 13px; font-weight: 700; color: var(--admin-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $page->page_title ?: __('admin.page') . ' ' . $page->page_number }}
                            </div>

                            <!-- Move Up / Down Buttons -->
                            <div style="display: flex; gap: 6px;">
                                <!-- Move Up -->
                                <form action="{{ route('admin.editions.reorder', $edition) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <input type="hidden" name="page_id" value="{{ $page->id }}">
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="btn-admin btn-outline" style="width: 100%; padding: 6px; justify-content: center; font-size: 11px;"
                                            {{ $page->page_number <= 1 ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}
                                            title="{{ __('admin.move_up') }}">
                                        <i class="fa-solid fa-arrow-up"></i> {{ __('admin.move_earlier') }}
                                    </button>
                                </form>

                                <!-- Move Down -->
                                <form action="{{ route('admin.editions.reorder', $edition) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <input type="hidden" name="page_id" value="{{ $page->id }}">
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="btn-admin btn-outline" style="width: 100%; padding: 6px; justify-content: center; font-size: 11px;"
                                            {{ $page->page_number >= $edition->total_pages ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}
                                            title="{{ __('admin.move_down') }}">
                                        {{ __('admin.move_later') }} <i class="fa-solid fa-arrow-down"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Metadata & Workflow Log -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Metadata Card -->
            <div class="card">
                <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 16px; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">
                    <i class="fa-solid fa-gear" style="color: var(--admin-gold); margin-right: 6px;"></i>
                    {{ __('admin.edit_edition_details') }}
                </h3>

                <form action="{{ route('admin.editions.update', $edition) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            {{ __('admin.edition_title') }}
                        </label>
                        <input type="text" name="title" value="{{ old('title', $edition->title) }}" required
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            {{ __('admin.volume') }}
                        </label>
                        <input type="text" name="volume_number" value="{{ old('volume_number', $edition->volume_number) }}"
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            {{ __('admin.issue_number') }}
                        </label>
                        <input type="text" name="issue_number" value="{{ old('issue_number', $edition->issue_number) }}"
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            {{ __('admin.scheduled_time') }}
                        </label>
                        <input type="datetime-local" name="scheduled_at" 
                               value="{{ $edition->scheduled_at ? \Carbon\Carbon::parse($edition->scheduled_at)->format('Y-m-d\TH:i') : '' }}"
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                        <span style="font-size: 11px; color: var(--admin-muted);">{{ __('admin.scheduled_time_hint') }}</span>
                    </div>

                    <button type="submit" class="btn-admin btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-floppy-disk"></i> {{ __('admin.save_changes') }}
                    </button>
                </form>
            </div>

            <!-- PDF Security & Storage Info -->
            <div class="card" style="background: #F8FAFC;">
                <h4 style="font-size: 14px; font-weight: 700; color: var(--admin-navy); margin-bottom: 12px;">
                    <i class="fa-solid fa-shield-halved" style="color: #10B981;"></i> {{ __('admin.security_storage_info') }}
                </h4>
                <div style="font-size: 12px; color: var(--admin-muted); display: flex; flex-direction: column; gap: 8px;">
                    <div><strong>{{ __('admin.original_pdf') }}:</strong> {{ basename($edition->pdf_path ?? 'system/pdf') }}</div>
                    <div><strong>{{ __('admin.storage_disk') }}:</strong> Private Local (Secured)</div>
                    <div><strong>{{ __('admin.views') }}:</strong> {{ number_format($edition->views_count) }} | <strong>{{ __('admin.downloads') }}:</strong> {{ number_format($edition->downloads_count) }}</div>
                </div>
            </div>

            <!-- Workflow Audit Trail Log -->
            <div class="card">
                <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 16px; color: var(--admin-navy); margin-bottom: 14px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--admin-gold); margin-right: 6px;"></i>
                    {{ __('admin.audit_trail') }}
                </h3>

                <div style="display: flex; flex-direction: column; gap: 14px; max-height: 380px; overflow-y: auto;">
                    @forelse($edition->workflowLogs as $log)
                        <div style="border-left: 2px solid var(--admin-gold); padding-left: 12px; font-size: 12px;">
                            <div style="font-weight: 700; color: var(--admin-navy);">
                                {{ strtoupper($log->action) }}
                                @if($log->to_status)
                                    <span style="color: var(--admin-muted); font-weight: normal;">&rarr; {{ $log->to_status }}</span>
                                @endif
                            </div>
                            <div style="color: #475569; margin-top: 2px;">{{ $log->notes }}</div>
                            <div style="color: #94A3B8; font-size: 11px; margin-top: 4px;">
                                {{ $log->user->name ?? __('admin.system') }} &bull; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--admin-muted); font-size: 12px;">{{ __('admin.no_audit_records') }}</div>
                    @endforelse
                </div>
            </div>

            <!-- Delete Edition -->
            @if(auth()->user()->canPublish())
                <div class="card" style="border-color: #FECACA; background: #FEF2F2;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #991B1B; margin-bottom: 8px;">
                        {{ __('admin.danger_zone') }}
                    </h4>
                    <p style="font-size: 12px; color: #7F1D1D; margin-bottom: 12px;">
                        {{ __('admin.delete_warning') }}
                    </p>
                    <form action="{{ route('admin.editions.destroy', $edition) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-admin btn-danger" style="width: 100%; justify-content: center; font-size: 12px;"
                                onclick="return confirm('{{ __('admin.delete_confirm') }}');">
                            <i class="fa-solid fa-trash"></i> {{ __('admin.delete_edition_btn') }}
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>

<!-- Modal for Rejection Reason -->
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 10px; width: 100%; max-width: 500px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 18px; color: #DC2626; margin-bottom: 12px;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ __('admin.reject_modal_title') }}
        </h3>
        <p style="font-size: 13px; color: var(--admin-muted); margin-bottom: 16px;">
            {{ __('admin.reject_modal_desc') }}
        </p>

        <form action="{{ route('admin.editions.reject', $edition) }}" method="POST">
            @csrf
            <textarea name="reason" rows="3" required placeholder="{{ __('admin.reject_placeholder') }}"
                      style="width: 100%; padding: 10px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px; margin-bottom: 16px;"></textarea>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-admin btn-outline" onclick="document.getElementById('rejectModal').style.display='none';">{{ __('admin.cancel') }}</button>
                <button type="submit" class="btn-admin btn-danger">{{ __('admin.reject_and_send') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    (function() {
        const masterPdfUrl = @json($edition->master_pdf_url);
        if (typeof pdfjsLib !== 'undefined' && masterPdfUrl) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            
            pdfjsLib.getDocument(masterPdfUrl).promise.then(pdf => {
                const canvases = document.querySelectorAll('.admin-page-thumb-canvas');
                canvases.forEach(canvas => {
                    const pageNum = parseInt(canvas.dataset.pageNum, 10);
                    if (pageNum && pageNum <= pdf.numPages) {
                        pdf.getPage(pageNum).then(page => {
                            const viewport = page.getViewport({ scale: 0.5 });
                            canvas.width = viewport.width;
                            canvas.height = viewport.height;
                            const ctx = canvas.getContext('2d');
                            page.render({ canvasContext: ctx, viewport: viewport }).promise.then(() => {
                                canvas.style.display = 'block';
                                const fallbackImg = canvas.parentElement.querySelector('img');
                                if (fallbackImg) fallbackImg.style.display = 'none';
                            });
                        });
                    }
                });
            }).catch(err => {
                console.warn('Admin PDF preview fallback:', err);
            });
        }
    })();
</script>
@endpush
