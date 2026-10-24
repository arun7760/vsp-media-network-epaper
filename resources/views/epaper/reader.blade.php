@extends('layouts.epaper')

@section('title', ($currentPage ? "పేజీ {$pageNumber}: {$currentPage->page_title} - " : "") . $edition->title . " | VSP TIMES")

@push('styles')
<style>
    /* Reader Layout Adjustments */
    main {
        padding: 0 0 40px 0 !important;
        background: #1E293B;
    }
    .content-container {
        max-width: 100% !important;
    }

    /* Top Control Toolbar */
    .reader-toolbar {
        background: #0F172A;
        border-bottom: 1px solid #334155;
        padding: 10px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }
    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 14px;
        color: #F8FAFC;
    }
    .toolbar-title {
        font-weight: 700;
        font-size: 15px;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .page-counter-badge {
        background: linear-gradient(135deg, var(--epaper-red) 0%, var(--epaper-red-dark) 100%);
        color: #FFFFFF;
        font-weight: 800;
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 999px;
        box-shadow: 0 2px 6px rgba(214, 0, 28, 0.4);
    }

    /* Zoom Controls */
    .toolbar-center {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tool-btn {
        background: #1E293B;
        color: #F8FAFC;
        border: 1px solid #475569;
        padding: 7px 14px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .tool-btn:hover {
        background: var(--epaper-blue);
        border-color: var(--epaper-blue);
        color: #FFFFFF;
        transform: translateY(-1px);
    }
    .tool-btn:active {
        transform: scale(0.96);
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Main Viewport */
    .reader-stage {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        min-height: calc(100vh - 280px);
        padding: 30px 20px;
        overflow: auto;
    }
    .page-canvas-wrapper {
        box-shadow: 0 16px 40px rgba(0,0,0,0.5);
        border: 1px solid #475569;
        border-radius: 4px;
        background: #FFFFFF;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        transform-origin: top center;
        max-width: 900px;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .page-canvas-wrapper img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 4px;
        user-select: none;
    }

    /* Float Navigation Arrows */
    .nav-arrow {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(15, 23, 42, 0.85);
        color: #FFFFFF;
        width: 48px;
        height: 64px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid rgba(255,255,255,0.15);
        z-index: 50;
        backdrop-filter: blur(4px);
    }
    .nav-arrow:hover {
        background: var(--epaper-red);
        color: #FFFFFF;
        border-color: var(--epaper-red);
    }
    .nav-arrow-left {
        left: 20px;
    }
    .nav-arrow-right {
        right: 20px;
    }

    /* Bottom Thumbnail Rail */
    .filmstrip-container {
        background: #0B132B;
        border-top: 1px solid #1E293B;
        padding: 16px 20px 20px;
        position: sticky;
        bottom: 0;
        z-index: 90;
    }
    .filmstrip-header {
        color: #94A3B8;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
    }
    .filmstrip-rail {
        display: flex;
        gap: 14px;
        overflow-x: auto;
        padding-bottom: 8px;
        scrollbar-width: thin;
        scrollbar-color: var(--epaper-red) #1E293B;
    }
    .thumb-card {
        flex: 0 0 110px;
        text-decoration: none;
        color: #CBD5E1;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .thumb-card:hover {
        transform: translateY(-3px);
    }
    .thumb-box {
        width: 100%;
        aspect-ratio: 1 / 1.35;
        background: #1E293B;
        border-radius: 4px;
        overflow: hidden;
        border: 2px solid transparent;
        position: relative;
    }
    .thumb-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumb-card.active .thumb-box {
        border-color: var(--epaper-red);
        box-shadow: 0 0 12px rgba(214, 0, 28, 0.5);
    }
    .thumb-page-num {
        position: absolute;
        bottom: 3px;
        right: 3px;
        background: rgba(0,0,0,0.8);
        color: #FFFFFF;
        font-size: 10px;
        font-weight: 800;
        padding: 1px 6px;
        border-radius: 3px;
    }
    .thumb-card.active .thumb-page-num {
        background: var(--epaper-red);
        color: #FFFFFF;
    }
    .thumb-title {
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        margin-top: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }
</style>
@endpush

@section('content')

    <!-- Top Action Toolbar -->
    <div class="reader-toolbar">
        <div class="toolbar-left">
            <a href="{{ route('epaper.home') }}" class="tool-btn" style="background: #FFFFFF; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.2);" title="హోమ్ పేజీకి తిరిగి వెళ్లండి">
                <img src="{{ asset('images/logo.png') }}" alt="VSP TIMES" style="height: 24px; width: auto; object-fit: contain;">
                <span style="color: var(--epaper-navy); font-weight: 700; font-size: 0.85rem;"><i class="fa-solid fa-arrow-left"></i> హోమ్</span>
            </a>
            <div class="toolbar-title">
                <span>{{ $edition->region->name ?? 'ప్రధాన సంచిక' }}</span>
                <span style="color: #64748B;">&bull;</span>
                <span style="color: #E2E8F0;">{{ $edition->edition_date->format('d M Y') }}</span>
            </div>
            <!-- Page Jump Selector -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <select onchange="window.location.href=this.value;" class="tool-btn" style="padding: 5px 10px; font-weight: 700; background: #00143E; border-color: var(--epaper-blue); color: #FFFFFF;">
                    @for($i = 1; $i <= $totalPages; $i++)
                        <option value="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $i]) }}" {{ $i == $pageNumber ? 'selected' : '' }}>
                            పేజీ {{ $i }} / {{ $totalPages }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Zoom & View Tools -->
        <div class="toolbar-center">
            <button class="tool-btn" id="btn-zoom-out" title="జూమ్ అవుట్"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
            <button class="tool-btn" id="btn-zoom-reset" title="పేజీ సరిపోల్చండి (Fit Page)"><i class="fa-solid fa-arrows-to-eye"></i> Fit Page</button>
            <button class="tool-btn" id="btn-fit-width" title="వెడల్పు సరిపోల్చండి (Fit Width)"><i class="fa-solid fa-arrows-left-right"></i> Fit Width</button>
            <button class="tool-btn" id="btn-zoom-in" title="జూమ్ ఇన్"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
            <button class="tool-btn" id="btn-fullscreen" title="పూర్తి స్క్రీన్"><i class="fa-solid fa-expand"></i></button>
        </div>

        <div class="toolbar-right">
            @if($edition->sibling_editions && count($siblingEditions) > 1)
                <select class="tool-btn" onchange="window.location.href=this.value;" style="outline: none;">
                    @foreach($siblingEditions as $sib)
                        <option value="{{ route('epaper.reader', $sib) }}" {{ $sib->id === $edition->id ? 'selected' : '' }}>
                            {{ $sib->region->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <a href="{{ route('epaper.download', $edition) }}" class="tool-btn" style="background: linear-gradient(135deg, var(--epaper-red) 0%, var(--epaper-red-dark) 100%); color: #FFFFFF; border-color: var(--epaper-red); box-shadow: 0 2px 8px rgba(214, 0, 28, 0.4); font-weight: 700;">
                <i class="fa-solid fa-download"></i> పీడీఎఫ్
            </a>
        </div>
    </div>

    <!-- Main Broadsheet Page Viewport -->
    <div class="reader-stage" id="reader-stage">
        @if($prevPageNumber)
            <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $prevPageNumber]) }}" class="nav-arrow nav-arrow-left" title="మునుపటి పేజీ (పేజీ {{ $prevPageNumber }})">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        @endif

        <div class="page-canvas-wrapper" id="page-wrapper">
            @if($currentPage)
                <img id="page-image" 
                     src="{{ $currentPage->image_url }}" 
                     alt="పేజీ {{ $pageNumber }} - {{ $currentPage->page_title }}" 
                     onerror="this.src='https://placehold.co/900x1270/0A1D37/FFFFFF/png?text='+encodeURIComponent('VSP TIMES\n{{ $edition->region?->name }}\nపేజీ {{ $pageNumber }} : {{ $currentPage->page_title }}');">
            @else
                <div style="padding: 100px 40px; text-align: center; color: #94A3B8;">
                    <i class="fa-solid fa-file-circle-exclamation" style="font-size: 40px; margin-bottom: 12px;"></i>
                    <p>ఈ పేజీ అందుబాటులో లేదు.</p>
                </div>
            @endif
        </div>

        @if($nextPageNumber)
            <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $nextPageNumber]) }}" class="nav-arrow nav-arrow-right" title="తదుపరి పేజీ (పేజీ {{ $nextPageNumber }})">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        @endif
    </div>

    <!-- Bottom Thumbnail Filmstrip Rail -->
    <div class="filmstrip-container">
        <div class="filmstrip-header">
            <span><i class="fa-solid fa-film"></i> అన్ని పేజీల జాబితా (Filmstrip) &bull; క్లిక్ చేసి పేజీకి వెళ్లండి</span>
            <span>మొత్తం {{ $totalPages }} పేజీలు</span>
        </div>
        <div class="filmstrip-rail">
            @foreach($edition->pages as $p)
                <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $p->page_number]) }}" 
                   class="thumb-card {{ $p->page_number === $pageNumber ? 'active' : '' }}">
                    <div class="thumb-box">
                        <img src="{{ $p->thumbnail_url }}" alt="పేజీ {{ $p->page_number }}" onerror="this.src='https://placehold.co/110x150/1E293B/FFFFFF/png?text='+encodeURIComponent('P.{{ $p->page_number }}');">
                        <span class="thumb-page-num">{{ $p->page_number }}</span>
                    </div>
                    <div class="thumb-title" title="{{ $p->page_title }}">{{ $p->page_title }}</div>
                </a>
            @endforeach
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Reader Zoom and Pan Engine
    (function() {
        let zoomLevel = 1.0;
        const pageWrapper = document.getElementById('page-wrapper');
        const btnIn = document.getElementById('btn-zoom-in');
        const btnOut = document.getElementById('btn-zoom-out');
        const btnReset = document.getElementById('btn-zoom-reset');
        const btnFs = document.getElementById('btn-fullscreen');

        const btnFitWidth = document.getElementById('btn-fit-width');

        function applyZoom() {
            pageWrapper.style.transform = `scale(${zoomLevel})`;
        }

        btnIn.addEventListener('click', () => {
            if (zoomLevel < 3.0) {
                zoomLevel += 0.25;
                applyZoom();
            }
        });

        btnOut.addEventListener('click', () => {
            if (zoomLevel > 0.6) {
                zoomLevel -= 0.25;
                applyZoom();
            }
        });

        btnReset.addEventListener('click', () => {
            zoomLevel = 1.0;
            pageWrapper.style.maxWidth = '900px';
            applyZoom();
        });

        if (btnFitWidth) {
            btnFitWidth.addEventListener('click', () => {
                const stageWidth = document.getElementById('reader-stage').clientWidth - 40;
                pageWrapper.style.maxWidth = stageWidth + 'px';
                zoomLevel = 1.0;
                applyZoom();
            });
        }

        // Double Click to Zoom in / Reset
        pageWrapper.addEventListener('dblclick', (e) => {
            if (zoomLevel === 1.0) {
                zoomLevel = 1.8;
            } else {
                zoomLevel = 1.0;
                pageWrapper.style.maxWidth = '900px';
            }
            applyZoom();
        });

        btnFs.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        });

        // Keyboard arrow navigation
        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                const prev = document.querySelector('.nav-arrow-left');
                if (prev) prev.click();
            } else if (e.key === 'ArrowRight') {
                const next = document.querySelector('.nav-arrow-right');
                if (next) next.click();
            }
        });

        // Mobile touch swipe gesture
        let touchStartX = 0;
        let touchEndX = 0;
        const stage = document.getElementById('reader-stage');

        stage.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        stage.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const swipeThreshold = 60;
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swiped Left -> Next page
                const next = document.querySelector('.nav-arrow-right');
                if (next) next.click();
            }
            if (touchEndX > touchStartX + swipeThreshold) {
                // Swiped Right -> Prev page
                const prev = document.querySelector('.nav-arrow-left');
                if (prev) prev.click();
            }
        }
    })();
</script>
@endpush
