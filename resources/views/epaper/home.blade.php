@extends('layouts.epaper')

@section('title', 'VSP TIMES ఈ-పేపర్ | తాజా దినపత్రిక డిజిటల్ ఎడిషన్')

@push('styles')
<style>
    /* Premium Newspaper Portal Layout */
    .portal-hero-container {
        max-width: 1240px;
        margin: 0 auto 40px;
    }

    /* Hero Spotlight Broadsheet Card */
    .hero-edition-card {
        background: #FFFFFF;
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 20px 50px rgba(10, 29, 55, 0.08), 0 4px 14px rgba(10, 29, 55, 0.03);
        padding: 44px;
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 50px;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .hero-edition-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--epaper-red) 0%, var(--epaper-gold) 50%, var(--epaper-blue) 100%);
    }

    .cover-wrapper {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: -10px 18px 40px rgba(15, 23, 42, 0.18), 0 2px 8px rgba(15, 23, 42, 0.06);
        border: 1px solid #CBD5E1;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s;
        background: #F8FAFC;
        aspect-ratio: 1 / 1.414;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cover-wrapper:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: -12px 24px 50px rgba(15, 23, 42, 0.24);
    }
    .cover-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        background: #FFFFFF;
    }
    .live-badge-overlay {
        position: absolute;
        top: 14px;
        right: 14px;
        background: rgba(10, 29, 55, 0.88);
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 999px;
        backdrop-filter: blur(6px);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .live-badge-dot {
        width: 7px;
        height: 7px;
        background: #10B981;
        border-radius: 50%;
        box-shadow: 0 0 8px #10B981;
    }

    /* Edition Details */
    .hero-kicker-strip {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .kicker-tag {
        font-size: 12px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 999px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .kicker-red {
        background: #FEE2E2;
        color: #B91C1C;
        border: 1px solid #FECACA;
    }
    .kicker-blue {
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
    }

    .hero-edition-title {
        font-family: {{ app()->getLocale() === 'en' ? "var(--font-sans)" : "var(--font-telugu-headline)" }};
        font-size: 34px;
        color: var(--epaper-navy);
        line-height: 1.25;
        margin-bottom: 16px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .edition-meta-strip {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 26px;
        padding-bottom: 20px;
        border-bottom: 1px solid #E2E8F0;
    }
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F8FAFC;
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        font-size: 13px;
        color: #475569;
        font-weight: 600;
    }
    .meta-pill strong {
        color: #0F172A;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }
    .btn-read-now {
        background: linear-gradient(135deg, var(--epaper-red) 0%, #A90D15 100%);
        color: #FFFFFF !important;
        font-size: 16px;
        font-weight: 800;
        padding: 14px 32px;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(214, 0, 28, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-read-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(214, 0, 28, 0.55);
        filter: brightness(1.08);
    }
    .btn-download-pdf {
        background: #FFFFFF;
        color: var(--epaper-navy) !important;
        border: 2px solid #CBD5E1;
        font-size: 15px;
        font-weight: 700;
        padding: 13px 26px;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-download-pdf:hover {
        border-color: var(--epaper-red);
        color: var(--epaper-red) !important;
        background: #FFF1F2;
        transform: translateY(-2px);
    }

    /* Page Rail Grid inside Card */
    .page-rail-heading {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .page-quick-jump {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .page-jump-chip {
        background: #F1F5F9;
        color: #1E293B;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        padding: 7px 14px;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .page-jump-chip:hover {
        background: var(--epaper-navy);
        color: #FFFFFF;
        border-color: var(--epaper-navy);
        transform: translateY(-1px);
    }

    /* Recent Editions Strip (Archive Highlights) */
    .recent-strip-container {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        padding: 24px 28px;
        box-shadow: 0 4px 18px rgba(10, 29, 55, 0.04);
        margin-top: 32px;
    }
    .strip-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .strip-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--epaper-navy);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .strip-link {
        font-size: 13px;
        font-weight: 700;
        color: var(--epaper-blue);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .strip-link:hover {
        color: var(--epaper-red);
    }
    .recent-dates-grid {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: thin;
    }
    .recent-date-card {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 12px 18px;
        text-decoration: none;
        color: #1E293B;
        min-width: 140px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.2s;
    }
    .recent-date-card:hover {
        background: #FFFFFF;
        border-color: var(--epaper-red);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(214, 0, 28, 0.12);
    }
    .recent-date-day {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748B;
        margin-bottom: 2px;
    }
    .recent-date-num {
        font-size: 16px;
        font-weight: 800;
        color: var(--epaper-navy);
    }

    @media (max-width: 900px) {
        .hero-edition-card {
            grid-template-columns: 1fr;
            padding: 24px;
            gap: 28px;
        }
        .cover-wrapper {
            max-width: 300px;
            margin: 0 auto;
        }
        .hero-edition-title {
            font-size: 26px;
        }
    }
</style>
@endpush

@section('content')
<div class="portal-hero-container">

    @if($edition)
        <!-- Hero Spotlight Broadsheet Card -->
        <div class="hero-edition-card">
            <!-- Left: Broadsheet Cover with 3D Depth -->
            <div class="cover-wrapper">
                <canvas id="hero-cover-canvas" style="display: none; width: 100%; height: auto; border-radius: 6px;"></canvas>
                <img id="hero-cover-img" src="{{ $edition->cover_url }}" alt="{{ $edition->title }}" onerror="this.src='/images/logo.png';">
                <span class="live-badge-overlay">
                    <span class="live-badge-dot"></span>
                    <span>{{ $edition->total_pages }} {{ __('epaper.pages') }} &bull; లైవ్</span>
                </span>
            </div>

            <!-- Right: Metadata & Actions -->
            <div class="edition-details">
                <div class="hero-kicker-strip">
                    <span class="kicker-tag kicker-red">
                        <i class="fa-solid fa-newspaper"></i> {{ $edition->region->name ?? __('epaper.main_edition') }}
                    </span>
                    <span class="kicker-tag kicker-blue">
                        <i class="fa-solid fa-bolt"></i> డిజిటల్ లైవ్ ఎడిషన్
                    </span>
                </div>

                <h1 class="hero-edition-title">{{ $edition->title }}</h1>

                <div class="edition-meta-strip">
                    <div class="meta-pill">
                        <i class="fa-regular fa-calendar-check" style="color: var(--epaper-red);"></i>
                        <span>{{ __('epaper.date') }}: <strong>{{ app()->getLocale() === 'te' && $edition->telugu_date_string ? $edition->telugu_date_string : $edition->edition_date->format('d M Y') }}</strong></span>
                    </div>
                    @if($edition->volume_number)
                        <div class="meta-pill">
                            <i class="fa-solid fa-layer-group" style="color: var(--epaper-blue);"></i>
                            <span>{{ $edition->volume_number }}</span>
                        </div>
                    @endif
                    @if($edition->issue_number)
                        <div class="meta-pill">
                            <i class="fa-regular fa-bookmark" style="color: #D97706;"></i>
                            <span>{{ $edition->issue_number }}</span>
                        </div>
                    @endif
                    <div class="meta-pill">
                        <i class="fa-regular fa-eye" style="color: #059669;"></i>
                        <span><strong>{{ number_format($edition->views_count) }}</strong> పాఠకులు</span>
                    </div>
                </div>

                <!-- Primary CTAs -->
                <div class="action-buttons">
                    <a href="{{ route('epaper.edition', $edition) }}" class="btn-read-now">
                        <i class="fa-solid fa-book-open-reader"></i>
                        <span>{{ __('epaper.read_online') }}</span>
                    </a>

                    <a href="{{ route('epaper.download', $edition) }}" class="btn-download-pdf">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                        <span>{{ __('epaper.download_pdf') }}</span>
                    </a>
                </div>

                <!-- Interactive Page Jump Strip -->
                <div>
                    <div class="page-rail-heading">
                        <i class="fa-solid fa-hand-pointer"></i>
                        <span>నేరుగా పేజీని ఎంచుకోండి (Quick Page Jump):</span>
                    </div>
                    <div class="page-quick-jump">
                        @foreach($edition->pages as $page)
                            <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $page->page_number]) }}" class="page-jump-chip">
                                <i class="fa-regular fa-file-lines"></i>
                                <span>పేజీ {{ $page->page_number }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Edition Available Fallback -->
        <div style="background: #FFFFFF; border-radius: 16px; padding: 60px 24px; text-align: center; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
            <i class="fa-regular fa-newspaper" style="font-size: 56px; color: #94A3B8; margin-bottom: 16px;"></i>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--epaper-navy); margin-bottom: 8px;">
                ఈ తేదీకి సంచిక ఇంకా ప్రచురించబడలేదు
            </h2>
            <p style="color: #64748B; font-size: 15px; margin-bottom: 24px;">
                దయచేసి ఆర్కైవ్ నుండి మునుపటి తేదీల దినపత్రికలను ఎంచుకోండి.
            </p>
            <a href="{{ route('epaper.archive') }}" class="btn-read-now" style="display: inline-flex;">
                <i class="fa-solid fa-box-archive"></i> ఆర్కైవ్ చూడండి
            </a>
        </div>
    @endif

    <!-- Recent Editions / Date Quick Browser Strip -->
    @if(isset($recentEditions) && $recentEditions->isNotEmpty())
        <div class="recent-strip-container">
            <div class="strip-header">
                <div class="strip-title">
                    <i class="fa-regular fa-calendar-days" style="color: var(--epaper-red);"></i>
                    <span>మునుపటి దినపత్రికలు (Recent Editions Archive):</span>
                </div>
                <a href="{{ route('epaper.archive') }}" class="strip-link">
                    <span>పూర్తి ఆర్కైవ్ క్యాలెండర్</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="recent-dates-grid">
                @foreach($recentEditions as $re)
                    <a href="{{ route('epaper.edition', $re) }}" class="recent-date-card">
                        <span class="recent-date-day">{{ $re->edition_date->format('D') }}</span>
                        <span class="recent-date-num">{{ $re->edition_date->format('d M Y') }}</span>
                        <span style="font-size: 11px; color: var(--epaper-red); font-weight: 700; margin-top: 4px;">{{ $re->total_pages }} పేజీలు</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

@if($edition && $edition->master_pdf_url)
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    (function() {
        const masterPdfUrl = @json($edition->master_pdf_url);
        if (typeof pdfjsLib !== 'undefined' && masterPdfUrl) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            pdfjsLib.getDocument(masterPdfUrl).promise.then(pdf => {
                pdf.getPage(1).then(page => {
                    const canvas = document.getElementById('hero-cover-canvas');
                    if (!canvas) return;
                    const viewport = page.getViewport({ scale: 0.8 });
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    const ctx = canvas.getContext('2d');
                    page.render({ canvasContext: ctx, viewport: viewport }).promise.then(() => {
                        canvas.style.display = 'block';
                        const img = document.getElementById('hero-cover-img');
                        if (img) img.style.display = 'none';
                    });
                });
            }).catch(err => console.warn('Home hero PDF preview fallback:', err));
        }
    })();
</script>
@endpush
@endif
