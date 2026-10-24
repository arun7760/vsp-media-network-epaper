@extends('layouts.admin')

@section('title', 'సంచిక ఎడిటర్ - ' . $edition->title)
@section('header_title', 'సంచిక నిర్వహణ & ఎడిటర్ (Edition Editor)')

@section('content')
<div>
    <!-- Top Action Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ route('admin.editions.index') }}" class="btn-admin btn-outline">
                <i class="fa-solid fa-arrow-left"></i> సంచికల జాబితా
            </a>
            <div>
                <h2 style="font-family: 'Ramabhadra', sans-serif; font-size: 22px; color: var(--admin-navy); margin-bottom: 2px;">
                    {{ $edition->title }}
                </h2>
                <div style="font-size: 13px; color: var(--admin-muted); display: flex; align-items: center; gap: 12px;">
                    <span><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($edition->edition_date)->format('d M Y') }}</span>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $edition->region->name_te }}</span>
                    <span><i class="fa-regular fa-file"></i> {{ $edition->total_pages }} పేజీలు</span>
                    <span>
                        @if($edition->status === 'published')
                            <span style="background: #ECFDF5; color: #065F46; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-circle-check"></i> లైవ్ (Published)
                            </span>
                        @elseif($edition->status === 'scheduled')
                            <span style="background: #EFF6FF; color: #1E40AF; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-clock"></i> షెడ్యూల్డ్ ({{ $edition->scheduled_at ? \Carbon\Carbon::parse($edition->scheduled_at)->format('d M, h:i A') : '5:00 AM' }})
                            </span>
                        @elseif($edition->status === 'pending_approval')
                            <span style="background: #FFFBEB; color: #92400E; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-hourglass-half"></i> ఆమోదం కొరకు వేచి ఉంది
                            </span>
                        @else
                            <span style="background: #F1F5F9; color: #475569; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                <i class="fa-solid fa-pencil"></i> డ్రాఫ్ట్ (Draft)
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Workflow Action Buttons -->
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- Public Preview Link -->
            @if($edition->status === 'published')
                <a href="{{ route('epaper.edition', $edition) }}" target="_blank" class="btn-admin btn-outline" style="border-color: #10B981; color: #065F46;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> రీడర్‌లో చూడండి
                </a>
            @else
                <a href="{{ route('epaper.edition', $edition) }}" target="_blank" class="btn-admin btn-outline" title="అడ్మిన్ ప్రీవ్యూ">
                    <i class="fa-solid fa-eye"></i> ప్రివ్యూ
                </a>
            @endif

            <!-- Submit for Review (if draft) -->
            @if($edition->status === 'draft' && auth()->user()->canUpload())
                <form action="{{ route('admin.editions.submit', $edition) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-admin btn-gold" onclick="return confirm('ఈ సంచికను ఎడిటోరియల్ ఆమోదం కొరకు పంపించాలా?');">
                        <i class="fa-solid fa-paper-plane"></i> ఆమోదం కొరకు పంపండి
                    </button>
                </form>
            @endif

            <!-- Approve Actions (for Reviewers / Admins) -->
            @if(in_array($edition->status, ['draft', 'pending_approval']) && auth()->user()->canApprove())
                <!-- Approve & Schedule Morning 5:00 AM -->
                <form action="{{ route('admin.editions.approve', $edition) }}" method="POST">
                    @csrf
                    <input type="hidden" name="publish_action" value="schedule_morning">
                    <button type="submit" class="btn-admin btn-primary" onclick="return confirm('ఉదయం 5:00 AM IST కి ఆటోమేటిక్ ప్రచురణ కొరకు షెడ్యూల్ చేయాలా?');">
                        <i class="fa-solid fa-clock"></i> ఆమోదించి 5:00 AM కి షెడ్యూల్ చేయండి
                    </button>
                </form>

                <!-- Immediate Publish (Admin/SuperAdmin) -->
                @if(auth()->user()->canPublish())
                    <form action="{{ route('admin.editions.publish', $edition) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-admin btn-success" onclick="return confirm('ఈ సంచికను ఇప్పుడే లైవ్ (Live Publish) చేయాలా?');">
                            <i class="fa-solid fa-bolt"></i> ఇప్పుడే లైవ్ చేయండి
                        </button>
                    </form>
                @endif

                <!-- Reject Button (Opens Modal) -->
                <button type="button" class="btn-admin btn-danger" onclick="document.getElementById('rejectModal').style.display='flex';">
                    <i class="fa-solid fa-ban"></i> తిరస్కరించండి
                </button>
            @endif

            <!-- Publish Now for Scheduled Edition -->
            @if($edition->status === 'scheduled' && auth()->user()->canPublish())
                <form action="{{ route('admin.editions.publish', $edition) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-admin btn-success" onclick="return confirm('షెడ్యూల్డ్ సంచికను వెంటనే లైవ్ చేయాలా?');">
                        <i class="fa-solid fa-bolt"></i> ఇప్పుడే లైవ్ చేయండి (Live Now)
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
                        పేజీల జాబితా &amp; వరుస క్రమం (Page Gallery)
                    </h3>
                    <p style="font-size: 13px; color: var(--admin-muted); margin-top: 2px;">
                        మొత్తం {{ $edition->pages->count() }} పేజీలు అందుబాటులో ఉన్నాయి. బాణాల (Arrows) ద్వారా వరుస క్రమాన్ని మార్చవచ్చు.
                    </p>
                </div>
                <div style="font-size: 12px; background: #F1F5F9; color: var(--admin-muted); padding: 5px 10px; border-radius: 6px; font-weight: 700;">
                    కవర్ పేజీ: 1 వ పేజీ
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
                            <img src="{{ $page->thumbnail_url }}" alt="పేజీ {{ $page->page_number }}" 
                                 onerror="this.src='/images/logo.png';"
                                 style="width: 100%; height: 100%; object-fit: contain;">
                            
                            <!-- Page Number Badge -->
                            <div style="position: absolute; top: 8px; left: 8px; background: var(--admin-navy); color: #FFFFFF; font-size: 12px; font-weight: 800; padding: 3px 8px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                పేజీ {{ $page->page_number }}
                            </div>

                            @if($page->page_number === 1)
                                <div style="position: absolute; top: 8px; right: 8px; background: var(--admin-gold); color: var(--admin-navy); font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 3px; text-transform: uppercase;">
                                    కవర్
                                </div>
                            @endif
                        </div>

                        <!-- Card Controls -->
                        <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px;">
                            <div style="font-size: 13px; font-weight: 700; color: var(--admin-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $page->page_title ?: 'పేజీ ' . $page->page_number }}
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
                                            title="పైకి జరపండి">
                                        <i class="fa-solid fa-arrow-up"></i> ముందు
                                    </button>
                                </form>

                                <!-- Move Down -->
                                <form action="{{ route('admin.editions.reorder', $edition) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <input type="hidden" name="page_id" value="{{ $page->id }}">
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="btn-admin btn-outline" style="width: 100%; padding: 6px; justify-content: center; font-size: 11px;"
                                            {{ $page->page_number >= $edition->total_pages ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}
                                            title="క్రిందికి జరపండి">
                                        తర్వాత <i class="fa-solid fa-arrow-down"></i>
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
                    సంచిక వివరాలు మార్చండి
                </h3>

                <form action="{{ route('admin.editions.update', $edition) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            సంచిక శీర్షిక
                        </label>
                        <input type="text" name="title" value="{{ old('title', $edition->title) }}" required
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            సంపుటి (Volume)
                        </label>
                        <input type="text" name="volume_number" value="{{ old('volume_number', $edition->volume_number) }}"
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            సంచిక సంఖ్య (Issue)
                        </label>
                        <input type="text" name="issue_number" value="{{ old('issue_number', $edition->issue_number) }}"
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                            షెడ్యూల్డ్ సమయం (Scheduled Time IST)
                        </label>
                        <input type="datetime-local" name="scheduled_at" 
                               value="{{ $edition->scheduled_at ? \Carbon\Carbon::parse($edition->scheduled_at)->format('Y-m-d\TH:i') : '' }}"
                               style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px; font-family: inherit;">
                        <span style="font-size: 11px; color: var(--admin-muted);">సాధారణంగా ప్రతి ఉదయం 05:00 AM IST</span>
                    </div>

                    <button type="submit" class="btn-admin btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-floppy-disk"></i> మార్పులను భద్రపరచండి
                    </button>
                </form>
            </div>

            <!-- PDF Security & Storage Info -->
            <div class="card" style="background: #F8FAFC;">
                <h4 style="font-size: 14px; font-weight: 700; color: var(--admin-navy); margin-bottom: 12px;">
                    <i class="fa-solid fa-shield-halved" style="color: #10B981;"></i> ఫైల్ భద్రతా సమాచారం
                </h4>
                <div style="font-size: 12px; color: var(--admin-muted); display: flex; flex-direction: column; gap: 8px;">
                    <div><strong>ఒరిజినల్ పీడీఎఫ్:</strong> {{ basename($edition->pdf_path ?? 'system/pdf') }}</div>
                    <div><strong>స్టోరేజ్ డిస్క్:</strong> Private Local (Secured)</div>
                    <div><strong>వ్యూస్:</strong> {{ number_format($edition->views_count) }} | <strong>డౌన్‌లోడ్స్:</strong> {{ number_format($edition->downloads_count) }}</div>
                </div>
            </div>

            <!-- Workflow Audit Trail Log -->
            <div class="card">
                <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 16px; color: var(--admin-navy); margin-bottom: 14px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--admin-gold); margin-right: 6px;"></i>
                    ఆడిట్ లాగ్ (Audit Trail)
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
                                {{ $log->user->name ?? 'సిస్టమ్' }} &bull; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div style="color: var(--admin-muted); font-size: 12px;">ఎటువంటి రికార్డులు లేవు.</div>
                    @endforelse
                </div>
            </div>

            <!-- Delete Edition -->
            @if(auth()->user()->canPublish())
                <div class="card" style="border-color: #FECACA; background: #FEF2F2;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #991B1B; margin-bottom: 8px;">
                        డేంజర్‌ జోన్
                    </h4>
                    <p style="font-size: 12px; color: #7F1D1D; margin-bottom: 12px;">
                        ఈ సంచికను తొలగిస్తే పబ్లిక్ రీడర్ నుండి తొలగించబడుతుంది (Soft delete).
                    </p>
                    <form action="{{ route('admin.editions.destroy', $edition) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-admin btn-danger" style="width: 100%; justify-content: center; font-size: 12px;"
                                onclick="return confirm('ఖచ్చితంగా ఈ సంచికను తొలగించాలనుకుంటున్నారా?');">
                            <i class="fa-solid fa-trash"></i> సంచికను తొలగించండి
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
            <i class="fa-solid fa-triangle-exclamation"></i> సంచికను తిరస్కరించండి
        </h3>
        <p style="font-size: 13px; color: var(--admin-muted); margin-bottom: 16px;">
            సంచికను డ్రాఫ్ట్ స్టేటస్‌కు పంపే ముందు డెస్క్ టీమ్‌కు కారణం తెలియజేయండి.
        </p>

        <form action="{{ route('admin.editions.reject', $edition) }}" method="POST">
            @csrf
            <textarea name="reason" rows="3" required placeholder="తిరస్కరణకు కారణం తెలపండి (ఉదా: పేజీ 3 లో ప్రకటన సరిగ్గా అమరలేదు)..."
                      style="width: 100%; padding: 10px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px; margin-bottom: 16px;"></textarea>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-admin btn-outline" onclick="document.getElementById('rejectModal').style.display='none';">రద్దు</button>
                <button type="submit" class="btn-admin btn-danger">తిరస్కరించి పంపండి</button>
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
