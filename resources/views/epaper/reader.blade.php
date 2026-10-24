@extends('layouts.epaper')

@section('title', ($currentPage ? "పేజీ {$pageNumber}: " . ($currentPage->page_title ?? 'ప్రధాన వార్తలు') . " - " : "") . $edition->title . " | VSP TIMES E-Paper")

@push('styles')
<style>
    /* Clean Distraction-Free Modern E-Paper Reader */
    body {
        overflow: hidden !important;
        background: #EBF1F6;
    }

    .reader-workspace {
        display: flex;
        flex-direction: column;
        height: 100vh;
        width: 100vw;
        overflow: hidden;
        position: relative;
        font-family: var(--font-sans);
    }

    /* 1. Sleek, Clean Top Header */
    .reader-header {
        height: 58px;
        min-height: 58px;
        background: #FFFFFF;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        z-index: 1000;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        gap: 12px;
    }

    .reader-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .reader-logo-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .reader-logo-img {
        height: 32px;
        width: auto;
        object-fit: contain;
    }

    .reader-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        color: #0F172A;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .reader-back-btn:hover {
        background: #E2E8F0;
        color: var(--epaper-red);
    }

    .reader-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 13px;
        color: #334155;
        font-weight: 600;
    }
    .reader-date-nav {
        color: #64748B;
        text-decoration: none;
        padding: 2px 6px;
        border-radius: 4px;
        transition: all 0.15s;
    }
    .reader-date-nav:hover {
        background: #E2E8F0;
        color: var(--epaper-red);
    }

    /* 2. Center: Page Navigation Controls */
    .reader-header-center {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .page-nav-arrow {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        color: #1E293B;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .page-nav-arrow:hover:not(.disabled) {
        background: var(--epaper-red);
        border-color: var(--epaper-red);
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(214, 0, 28, 0.3);
    }
    .page-nav-arrow.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        background: #F1F5F9;
    }

    .page-select-wrap {
        position: relative;
    }
    .page-select {
        height: 34px;
        padding: 0 28px 0 12px;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #0F172A;
        font-family: inherit;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s;
        appearance: none;
        -webkit-appearance: none;
    }
    .page-select:focus {
        border-color: var(--epaper-red);
    }
    .page-select-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #64748B;
        font-size: 10px;
    }

    .page-badge-counter {
        background: #0F172A;
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 999px;
    }

    /* 3. Right: Tools & Utilities */
    .reader-header-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .tool-group {
        display: inline-flex;
        align-items: center;
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        padding: 2px;
    }
    .btn-reader-tool {
        background: transparent;
        border: none;
        color: #334155;
        height: 30px;
        padding: 0 10px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }
    .btn-reader-tool:hover {
        background: #FFFFFF;
        color: #0F172A;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .zoom-level-indicator {
        font-size: 12px;
        font-weight: 800;
        color: #0F172A;
        min-width: 44px;
        text-align: center;
    }

    .btn-filmstrip-trigger {
        height: 34px;
        padding: 0 12px;
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #0F172A;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-filmstrip-trigger:hover, .btn-filmstrip-trigger.active {
        background: #0F172A;
        color: #FFFFFF;
        border-color: #0F172A;
    }

    .btn-download-pill {
        height: 34px;
        padding: 0 14px;
        background: linear-gradient(135deg, var(--epaper-red) 0%, #B80018 100%);
        border: none;
        border-radius: 6px;
        color: #FFFFFF !important;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(214, 0, 28, 0.35);
        transition: all 0.2s;
    }
    .btn-download-pill:hover {
        filter: brightness(1.1);
        transform: translateY(-1px);
    }

    .lang-pill-switch {
        display: inline-flex;
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        padding: 2px;
    }
    .lang-pill-opt {
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 4px;
        text-decoration: none;
        color: #64748B;
        transition: all 0.15s;
    }
    .lang-pill-opt.active {
        background: #0F172A;
        color: #FFFFFF;
    }

    /* 4. Canvas Stage */
    .reader-stage {
        flex: 1;
        overflow: auto;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 24px 20px 40px;
        position: relative;
        background: #EBF1F6;
        user-select: none;
    }

    .page-viewport-card {
        background: #FFFFFF;
        box-shadow: 0 14px 45px rgba(15, 23, 42, 0.14), 0 2px 8px rgba(15, 23, 42, 0.05);
        border: 1px solid #D8E1E8;
        border-radius: 4px;
        max-width: 960px;
        width: 100%;
        margin: 0 auto;
        transform-origin: top center;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: grab;
    }
    .page-viewport-card:active {
        cursor: grabbing;
    }

    .broadsheet-img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 4px;
        background: #FFFFFF;
    }

    /* 5. Floating Screen-Edge Chevrons */
    .screen-edge-arrow {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 68px;
        background: #FFFFFF;
        color: #0F172A;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.12);
        z-index: 500;
        transition: all 0.2s ease;
    }
    .screen-edge-arrow:hover {
        background: var(--epaper-red);
        border-color: var(--epaper-red);
        color: #FFFFFF;
        box-shadow: 0 6px 20px rgba(214, 0, 28, 0.4);
        transform: translateY(-50%) scale(1.05);
    }
    .screen-edge-arrow-left {
        left: 16px;
    }
    .screen-edge-arrow-right {
        right: 16px;
    }

    /* 6. Sleek Bottom Filmstrip Rail */
    .filmstrip-drawer {
        background: #FFFFFF;
        border-top: 2px solid #E2E8F0;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
        z-index: 900;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s;
        display: flex;
        flex-direction: column;
    }
    .filmstrip-drawer.collapsed {
        transform: translateY(100%);
        display: none;
    }

    .filmstrip-drawer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 18px;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }
    .filmstrip-close-btn {
        background: transparent;
        border: none;
        color: #64748B;
        font-size: 14px;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
        transition: all 0.15s;
    }
    .filmstrip-close-btn:hover {
        background: #E2E8F0;
        color: #0F172A;
    }

    .filmstrip-scroll-rail {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 18px;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--epaper-red) #E2E8F0;
    }

    .filmstrip-thumb {
        flex: 0 0 68px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #475569;
        transition: transform 0.2s;
    }
    .filmstrip-thumb:hover {
        transform: translateY(-2px);
    }
    .thumb-preview-box {
        width: 100%;
        aspect-ratio: 1 / 1.414;
        background: #F1F5F9;
        border: 2px solid #CBD5E1;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .thumb-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .filmstrip-thumb.active .thumb-preview-box {
        border-color: var(--epaper-red);
        box-shadow: 0 0 0 2px rgba(214, 0, 28, 0.25), 0 4px 12px rgba(214, 0, 28, 0.3);
    }
    .thumb-badge {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background: rgba(15, 23, 42, 0.85);
        color: #FFFFFF;
        font-size: 9px;
        font-weight: 800;
        padding: 1px 4px;
        border-radius: 2px;
    }
    .filmstrip-thumb.active .thumb-badge {
        background: var(--epaper-red);
    }
    .thumb-label {
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
        color: #0F172A;
    }

    /* 7. Responsive Breakpoints */
    @media (max-width: 1024px) {
        .reader-date-pill {
            display: none;
        }
        .btn-reader-tool span {
            display: none;
        }
        .screen-edge-arrow {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .reader-header {
            padding: 0 8px;
            height: 52px;
            min-height: 52px;
            gap: 6px;
        }
        .reader-back-btn span {
            display: none;
        }
        .btn-download-pill span {
            display: none;
        }
        .tool-group {
            display: none;
        }
        .btn-filmstrip-trigger span {
            display: none;
        }
        .reader-stage {
            padding: 10px 8px 24px;
        }
        .page-select {
            max-width: 170px;
            font-size: 12px;
            padding-right: 22px;
        }
        .page-badge-counter {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .reader-header {
            gap: 4px;
        }
        .reader-logo-img {
            height: 26px;
        }
        .page-select {
            max-width: 125px;
            font-size: 11px;
            padding-left: 8px;
            padding-right: 20px;
        }
        .page-nav-arrow {
            width: 30px;
            height: 30px;
            font-size: 11px;
        }
        .btn-download-pill, .btn-filmstrip-trigger, .reader-back-btn {
            padding: 0 8px;
            height: 30px;
        }
        .lang-pill-opt {
            padding: 3px 6px;
            font-size: 11px;
        }
    }
</style>
@endpush

@section('content')
<div class="reader-workspace">

    <!-- 1. Sleek, Clean Top Header Bar -->
    <header class="reader-header">
        <!-- Left: Logo, Home, Date, Edition -->
        <div class="reader-header-left">
            <a href="{{ route('epaper.home') }}" class="reader-logo-wrap" title="VSP TIMES E-Paper Home">
                <img src="/images/logo.png" alt="VSP TIMES" class="reader-logo-img">
            </a>

            <a href="{{ route('epaper.home') }}" class="reader-back-btn" title="హోమ్ పేజీకి తిరిగి వెళ్లండి">
                <i class="fa-solid fa-arrow-left"></i>
                <span>హోమ్</span>
            </a>

            <div class="reader-date-pill">
                @if($prevEdition)
                    <a href="{{ route('epaper.reader', $prevEdition) }}" class="reader-date-nav" title="మునుపటి సంచిక: {{ $prevEdition->edition_date->format('d M Y') }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                @endif

                <span><i class="fa-regular fa-calendar" style="color: var(--epaper-red); margin-right: 4px;"></i> {{ $edition->edition_date->format('d M Y') }}</span>

                @if($nextEdition)
                    <a href="{{ route('epaper.reader', $nextEdition) }}" class="reader-date-nav" title="తదుపరి సంచిక: {{ $nextEdition->edition_date->format('d M Y') }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                @endif

                <span style="color: #CBD5E1; margin: 0 2px;">|</span>
                <strong style="color: var(--epaper-navy);">{{ $edition->region->name ?? 'ప్రధాన సంచిక' }}</strong>
            </div>
        </div>

        <!-- Center: Clean Page Navigation -->
        <div class="reader-header-center">
            @if($prevPageNumber)
                <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $prevPageNumber]) }}" class="page-nav-arrow" title="మునుపటి పేజీ (పేజీ {{ $prevPageNumber }})">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @else
                <span class="page-nav-arrow disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @endif

            <div class="page-select-wrap">
                <select id="pageSelect" class="page-select" onchange="window.location.href=this.value;">
                    @foreach($edition->pages as $p)
                        <option value="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $p->page_number]) }}" {{ $p->page_number === $pageNumber ? 'selected' : '' }}>
                            పేజీ {{ $p->page_number }}: {{ $p->page_title ?: 'పేజీ ' . $p->page_number }}
                        </option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down page-select-icon"></i>
            </div>

            @if($nextPageNumber)
                <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $nextPageNumber]) }}" class="page-nav-arrow" title="తదుపరి పేజీ (పేజీ {{ $nextPageNumber }})">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-nav-arrow disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif

            <span class="page-badge-counter">పేజీ {{ $pageNumber }} / {{ $totalPages }}</span>
        </div>

        <!-- Right: Zoom, Filmstrip, Download, Language -->
        <div class="reader-header-right">
            <!-- Zoom Controls -->
            <div class="tool-group">
                <button type="button" class="btn-reader-tool" id="btn-zoom-out" title="జూమ్ అవుట్ (Ctrl + -)">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <span id="zoomPercent" class="zoom-level-indicator">100%</span>
                <button type="button" class="btn-reader-tool" id="btn-zoom-in" title="జూమ్ ఇన్ (Ctrl + +)">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button type="button" class="btn-reader-tool" id="btn-fit-page" title="పేజీని సరిపోల్చండి (Fit Page)">
                    <i class="fa-solid fa-arrows-to-eye"></i> <span>Fit Page</span>
                </button>
                <button type="button" class="btn-reader-tool" id="btn-fit-width" title="వెడల్పు సరిపోల్చండి (Fit Width)">
                    <i class="fa-solid fa-arrows-left-right"></i> <span>Fit Width</span>
                </button>
                <button type="button" class="btn-reader-tool" id="btn-fullscreen" title="పూర్తి స్క్రీన్ (Fullscreen)">
                    <i class="fa-solid fa-expand"></i>
                </button>
            </div>

            <!-- Filmstrip Toggle -->
            <button type="button" id="toggleFilmstripBtn" class="btn-filmstrip-trigger active" title="పేజీల థంబ్‌నెయిల్స్ చూడండి/దాచండి">
                <i class="fa-solid fa-images"></i>
                <span>పేజీలు (Filmstrip)</span>
                <i class="fa-solid fa-chevron-down" id="filmstripChevron"></i>
            </button>

            <!-- Download PDF -->
            <a href="{{ route('epaper.download', $edition) }}" 
               onclick="if(window.vspAnalytics){window.vspAnalytics.track('epaper_pdf_download', {edition_id: {{ $edition->id }}, edition_title: '{{ addslashes($edition->title) }}'});}" 
               class="btn-download-pill" 
               title="పూర్తి సంచిక పీడీఎఫ్ డౌన్‌లోడ్ చేయండి">
                <i class="fa-solid fa-cloud-arrow-down"></i>
                <span>{{ __('epaper.download_pdf') }}</span>
            </a>

            <!-- Language Switcher -->
            <div class="lang-pill-switch">
                <a href="{{ route('locale.switch', 'en') }}" class="lang-pill-opt {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('locale.switch', 'te') }}" class="lang-pill-opt {{ app()->getLocale() === 'te' ? 'active' : '' }}">తెలుగు</a>
            </div>
        </div>
    </header>

    <!-- 2. Main Canvas Viewport -->
    <div class="reader-stage" id="reader-stage">
        @if($prevPageNumber)
            <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $prevPageNumber]) }}" class="screen-edge-arrow screen-edge-arrow-left" title="మునుపటి పేజీ (పేజీ {{ $prevPageNumber }})">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        @endif

        <div class="page-viewport-card" id="page-wrapper">
            @if($currentPage)
                <img id="page-image" 
                     class="broadsheet-img"
                     src="{{ $currentPage->image_url }}" 
                     alt="పేజీ {{ $pageNumber }} - {{ $currentPage->page_title }}" 
                     onerror="this.src='/images/logo.png';">
            @else
                <div style="padding: 120px 40px; text-align: center; color: #64748B;">
                    <i class="fa-solid fa-file-circle-exclamation" style="font-size: 48px; color: #94A3B8; margin-bottom: 14px;"></i>
                    <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 6px;">ఈ పేజీ అందుబాటులో లేదు</h3>
                    <p style="font-size: 14px;">దయచేసి వేరొక పేజీని ఎంచుకోండి.</p>
                </div>
            @endif
        </div>

        @if($nextPageNumber)
            <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $nextPageNumber]) }}" class="screen-edge-arrow screen-edge-arrow-right" title="తదుపరి పేజీ (పేజీ {{ $nextPageNumber }})">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        @endif
    </div>

    <!-- 3. Sleek Bottom Filmstrip Rail -->
    <div class="filmstrip-drawer" id="filmstripDrawer">
        <div class="filmstrip-drawer-header">
            <span>
                <i class="fa-solid fa-film" style="color: var(--epaper-red); margin-right: 6px;"></i>
                అన్ని పేజీల జాబితా (Filmstrip) &bull; క్లిక్ చేసి నేరుగా పేజీకి వెళ్లండి
            </span>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span>మొత్తం {{ $totalPages }} పేజీలు</span>
                <button type="button" class="filmstrip-close-btn" id="closeFilmstripBtn" title="దాచండి">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="filmstrip-scroll-rail" id="filmstripRail">
            @foreach($edition->pages as $p)
                <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $p->page_number]) }}" 
                   class="filmstrip-thumb {{ $p->page_number === $pageNumber ? 'active' : '' }}"
                   title="పేజీ {{ $p->page_number }}: {{ $p->page_title }}">
                    <div class="thumb-preview-box">
                        <img src="{{ $p->thumbnail_url }}" alt="పేజీ {{ $p->page_number }}" onerror="this.src='/images/logo.png';">
                        <span class="thumb-badge">{{ $p->page_number }}</span>
                    </div>
                    <span class="thumb-label">పేజీ {{ $p->page_number }}</span>
                </a>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (function() {
        let zoomLevel = 1.0;
        const pageWrapper = document.getElementById('page-wrapper');
        const zoomPercentEl = document.getElementById('zoomPercent');
        const btnIn = document.getElementById('btn-zoom-in');
        const btnOut = document.getElementById('btn-zoom-out');
        const btnFitPage = document.getElementById('btn-fit-page');
        const btnFitWidth = document.getElementById('btn-fit-width');
        const btnFs = document.getElementById('btn-fullscreen');
        const readerStage = document.getElementById('reader-stage');

        const toggleFilmstripBtn = document.getElementById('toggleFilmstripBtn');
        const closeFilmstripBtn = document.getElementById('closeFilmstripBtn');
        const filmstripDrawer = document.getElementById('filmstripDrawer');
        const filmstripChevron = document.getElementById('filmstripChevron');

        function updateZoomDisplay() {
            if (zoomPercentEl) {
                zoomPercentEl.textContent = Math.round(zoomLevel * 100) + '%';
            }
        }

        function applyZoom() {
            if (pageWrapper) {
                pageWrapper.style.transform = `scale(${zoomLevel})`;
                updateZoomDisplay();
            }
        }

        if (btnIn) {
            btnIn.addEventListener('click', () => {
                if (zoomLevel < 2.6) {
                    zoomLevel = Math.min(2.6, zoomLevel + 0.2);
                    applyZoom();
                }
            });
        }

        if (btnOut) {
            btnOut.addEventListener('click', () => {
                if (zoomLevel > 0.6) {
                    zoomLevel = Math.max(0.6, zoomLevel - 0.2);
                    applyZoom();
                }
            });
        }

        if (btnFitPage) {
            btnFitPage.addEventListener('click', () => {
                zoomLevel = 1.0;
                pageWrapper.style.maxWidth = '960px';
                applyZoom();
            });
        }

        if (btnFitWidth) {
            btnFitWidth.addEventListener('click', () => {
                const stageWidth = readerStage.clientWidth - 48;
                pageWrapper.style.maxWidth = stageWidth + 'px';
                zoomLevel = 1.0;
                applyZoom();
            });
        }

        // Double Click to Toggle 1.8x Zoom / Fit
        if (pageWrapper) {
            pageWrapper.addEventListener('dblclick', (e) => {
                if (zoomLevel === 1.0) {
                    zoomLevel = 1.7;
                } else {
                    zoomLevel = 1.0;
                    pageWrapper.style.maxWidth = '960px';
                }
                applyZoom();
            });
        }

        // Fullscreen Toggle
        if (btnFs) {
            btnFs.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                } else {
                    document.exitFullscreen().catch(() => {});
                }
            });
        }

        // Filmstrip Toggle
        function toggleFilmstrip() {
            const isCollapsed = filmstripDrawer.classList.toggle('collapsed');
            toggleFilmstripBtn.classList.toggle('active', !isCollapsed);
            if (filmstripChevron) {
                filmstripChevron.className = isCollapsed ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
            }
        }

        if (toggleFilmstripBtn) {
            toggleFilmstripBtn.addEventListener('click', toggleFilmstrip);
        }
        if (closeFilmstripBtn) {
            closeFilmstripBtn.addEventListener('click', toggleFilmstrip);
        }

        // Drag to Pan when zoomed in
        let isDragging = false;
        let startX, startY, scrollLeft, scrollTop;

        readerStage.addEventListener('mousedown', (e) => {
            if (zoomLevel > 1.0) {
                isDragging = true;
                startX = e.pageX - readerStage.offsetLeft;
                startY = e.pageY - readerStage.offsetTop;
                scrollLeft = readerStage.scrollLeft;
                scrollTop = readerStage.scrollTop;
            }
        });

        readerStage.addEventListener('mouseleave', () => { isDragging = false; });
        readerStage.addEventListener('mouseup', () => { isDragging = false; });

        readerStage.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - readerStage.offsetLeft;
            const y = e.pageY - readerStage.offsetTop;
            const walkX = (x - startX) * 1.5;
            const walkY = (y - startY) * 1.5;
            readerStage.scrollLeft = scrollLeft - walkX;
            readerStage.scrollTop = scrollTop - walkY;
        });

        // Keyboard Navigation (Arrow Keys, +, -, F, W)
        window.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;

            if (e.key === 'ArrowLeft') {
                const prev = document.querySelector('.screen-edge-arrow-left');
                if (prev) prev.click();
            } else if (e.key === 'ArrowRight') {
                const next = document.querySelector('.screen-edge-arrow-right');
                if (next) next.click();
            } else if (e.key === '+' || e.key === '=') {
                btnIn && btnIn.click();
            } else if (e.key === '-' || e.key === '_') {
                btnOut && btnOut.click();
            } else if (e.key === 'f' || e.key === 'F') {
                btnFitPage && btnFitPage.click();
            } else if (e.key === 'w' || e.key === 'W') {
                btnFitWidth && btnFitWidth.click();
            }
        });

        // Mobile Touch Gestures (Swipe for page turning)
        let touchStartX = 0;
        let touchEndX = 0;

        readerStage.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        readerStage.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const swipeThreshold = 60;
            if (zoomLevel <= 1.1) {
                if (touchEndX < touchStartX - swipeThreshold) {
                    const next = document.querySelector('.screen-edge-arrow-right');
                    if (next) next.click();
                } else if (touchEndX > touchStartX + swipeThreshold) {
                    const prev = document.querySelector('.screen-edge-arrow-left');
                    if (prev) prev.click();
                }
            }
        }, { passive: true });

        // First-party and GA4 Readership Analytics Tracking
        if (window.vspAnalytics) {
            window.vspAnalytics.track('epaper_reader_open', {
                edition_id: {{ $edition->id }},
                edition_title: '{{ addslashes($edition->title) }}',
                page_number: {{ $pageNumber }},
                total_pages: {{ $totalPages }},
                publication_date: '{{ $edition->edition_date->toDateString() }}',
                region: '{{ addslashes($edition->region?->name ?? "General") }}'
            });

            @if($pageNumber > 1)
            window.vspAnalytics.track('epaper_page_navigation', {
                edition_id: {{ $edition->id }},
                page_number: {{ $pageNumber }},
                total_pages: {{ $totalPages }}
            });
            @endif
        }
    })();
</script>
@endpush
