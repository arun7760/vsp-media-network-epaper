@extends('layouts.epaper')

@section('title', 'VSP TIMES ఈ-పేపర్ | తాజా దినపత్రిక డిజిటల్ ఎడిషన్')

@push('styles')
<style>
    /* Region Nav Pills */
    .region-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #FFFFFF;
        padding: 12px 18px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        margin-bottom: 28px;
        overflow-x: auto;
        border: 1px solid var(--epaper-border);
    }
    .region-label {
        font-weight: 700;
        font-size: 14px;
        color: var(--epaper-navy);
        display: flex;
        align-items: center;
        gap: 6px;
        padding-right: 12px;
        border-right: 2px solid var(--epaper-border);
        white-space: nowrap;
    }
    .region-pill {
        padding: 8px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        color: var(--epaper-text);
        background: #F1F5F9;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .region-pill:hover {
        background: #E2E8F0;
        color: var(--epaper-navy);
    }
    .region-pill.active {
        background: var(--epaper-navy);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(10,29,55,0.18);
    }

    /* Hero Broadsheet Card */
    .hero-edition-card {
        background: #FFFFFF;
        border-radius: 14px;
        border: 1px solid var(--epaper-border);
        box-shadow: 0 10px 30px rgba(10,29,55,0.07);
        padding: 32px;
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 40px;
        align-items: center;
        margin-bottom: 40px;
    }
    .cover-wrapper {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        border: 1px solid #CBD5E1;
        transition: transform 0.3s ease;
        background: #F8FAFC;
        aspect-ratio: 1 / 1.414;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cover-wrapper:hover {
        transform: scale(1.02);
    }
    .cover-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .page-badge-overlay {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(10, 29, 55, 0.85);
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        backdrop-filter: blur(4px);
    }

    .edition-details h1 {
        font-family: var(--font-telugu-serif);
        font-size: 32px;
        color: var(--epaper-navy);
        line-height: 1.25;
        margin-bottom: 12px;
    }
    .edition-meta-strip {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        color: var(--epaper-muted);
        font-size: 14px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--epaper-border);
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .meta-item strong {
        color: var(--epaper-text);
    }

    .action-buttons {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }
    .btn-read-now {
        background: linear-gradient(135deg, #0A1D37 0%, #1A365D 100%);
        color: #FFFFFF;
        font-size: 16px;
        font-weight: 700;
        padding: 14px 28px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 14px rgba(10,29,55,0.25);
        transition: all 0.2s ease;
    }
    .btn-read-now:hover {
        background: linear-gradient(135deg, var(--epaper-blue) 0%, var(--epaper-navy) 100%);
        color: #FFFFFF;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 71, 171, 0.4);
    }
    .btn-download-pdf {
        background: #FFFFFF;
        color: var(--epaper-navy);
        border: 2px solid var(--epaper-navy);
        font-size: 15px;
        font-weight: 700;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-download-pdf:hover {
        background: #F8FAFC;
        border-color: var(--epaper-blue);
        color: var(--epaper-blue);
    }

    /* Page Rail Grid inside Card */
    .page-rail-heading {
        font-size: 15px;
        font-weight: 700;
        color: var(--epaper-navy);
        margin-bottom: 12px;
    }
    .page-quick-jump {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .page-jump-chip {
        background: #F1F5F9;
        color: var(--epaper-text);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid var(--epaper-border);
        transition: all 0.15s;
    }
    .page-jump-chip:hover {
        background: var(--epaper-blue);
        color: #FFFFFF;
        border-color: var(--epaper-blue);
    }

    /* Other Regional Editions */
    .section-title {
        font-family: var(--font-telugu-serif);
        font-size: 22px;
        color: var(--epaper-navy);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .editions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }
    .edition-subcard {
        background: #FFFFFF;
        border: 1px solid var(--epaper-border);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
    }
    .edition-subcard:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.09);
    }
    .edition-subcard-thumb {
        aspect-ratio: 1 / 1.35;
        background: #EEF2F6;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .edition-subcard-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .edition-subcard-info {
        padding: 16px;
    }
    .edition-subcard-title {
        font-weight: 700;
        font-size: 16px;
        color: var(--epaper-navy);
        margin-bottom: 4px;
    }
    .edition-subcard-date {
        font-size: 13px;
        color: var(--epaper-muted);
    }

    @media (max-width: 900px) {
        .hero-edition-card {
            grid-template-columns: 1fr;
            padding: 20px;
            gap: 24px;
        }
        .cover-wrapper {
            max-width: 280px;
            margin: 0 auto;
        }
    }
</style>
@endpush

@section('content')

    <!-- Regional Edition Selector Tabs -->
    <div class="region-nav">
        <div class="region-label">
            <i class="fa-solid fa-location-dot"></b> ఎడిషన్ ఎంచుకోండి:
        </div>
        @foreach($regions as $region)
            <a href="{{ route('epaper.home', ['region' => $region->slug, 'date' => request('date')]) }}" 
               class="region-pill {{ ($selectedRegion && $selectedRegion->id === $region->id) ? 'active' : '' }}">
                {{ $region->name }}
            </a>
        @endforeach
    </div>

    @if($edition)
        <!-- Hero Spotlight Card -->
        <div class="hero-edition-card">
            <div class="cover-wrapper">
                <img src="{{ $edition->cover_url }}" alt="{{ $edition->title }}" onerror="this.src='https://placehold.co/600x850/0A1D37/FFFFFF/png?text=VSP+TIMES+E-PAPER';">
                <span class="page-badge-overlay"><i class="fa-regular fa-file"></i> {{ $edition->total_pages }} పేజీలు</span>
            </div>

            <div class="edition-details">
                <div style="color: var(--epaper-red); font-weight: 800; font-size: 14px; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px;">
                    <i class="fa-regular fa-newspaper"></i> {{ $edition->region->name ?? 'ప్రధాన సంచిక' }}
                </div>
                <h1>{{ $edition->title }}</h1>

                <div class="edition-meta-strip">
                    <div class="meta-item">
                        <i class="fa-regular fa-calendar-check" style="color: var(--epaper-blue);"></i>
                        <span>తేదీ: <strong>{{ $edition->telugu_date_string ?: $edition->edition_date->format('d M Y') }}</strong></span>
                    </div>
                    @if($edition->volume_number)
                        <div class="meta-item">
                            <span>{{ $edition->volume_number }}</span>
                        </div>
                    @endif
                    @if($edition->issue_number)
                        <div class="meta-item">
                            <span>{{ $edition->issue_number }}</span>
                        </div>
                    @endif
                    <div class="meta-item">
                        <i class="fa-regular fa-eye"></i>
                        <span><strong>{{ number_format($edition->views_count) }}</strong> వీక్షణలు</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => 1]) }}" class="btn-read-now">
                        <i class="fa-solid fa-book-open-reader"></i> ఇప్పుడే చదవండి (Read Online)
                    </a>
                    <a href="{{ route('epaper.download', $edition) }}" class="btn-download-pdf">
                        <i class="fa-solid fa-file-pdf" style="color: var(--epaper-red);"></i> పూర్తి పీడీఎఫ్ డౌన్‌లోడ్
                    </a>
                </div>

                <!-- Quick Page Jump Chips -->
                <div>
                    <div class="page-rail-heading">
                        <i class="fa-solid fa-list-ol"></i> పేజీ వారీగా నేరుగా చదవండి:
                    </div>
                    <div class="page-quick-jump">
                        @foreach($edition->pages as $p)
                            <a href="{{ route('epaper.reader', ['edition' => $edition, 'pageNumber' => $p->page_number]) }}" class="page-jump-chip">
                                పేజీ {{ $p->page_number }}: {{ Str::limit($p->page_title, 18) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <div style="background: #FFFFFF; padding: 48px; border-radius: 12px; text-align: center; border: 1px solid var(--epaper-border);">
            <i class="fa-regular fa-newspaper" style="font-size: 48px; color: var(--epaper-muted); margin-bottom: 16px;"></i>
            <h2 style="font-size: 22px; color: var(--epaper-navy); margin-bottom: 8px;">ఎంచుకున్న తేదీకి ఈ-పేపర్ అందుబాటులో లేదు</h2>
            <p style="color: var(--epaper-muted); margin-bottom: 24px;">దయచేసి వేరొక తేదీ లేదా వేరొక ప్రాంతీయ ఎడిషన్‌ను ఎంచుకోండి.</p>
            <a href="{{ route('epaper.archive') }}" class="btn-epaper btn-epaper-red">ఆర్కైవ్‌ను బ్రౌజ్ చేయండి</a>
        </div>
    @endif

    <!-- Other Regional Editions Published on this Date -->
    @if(isset($otherEditions) && $otherEditions->isNotEmpty())
        <div class="section-title">
            <i class="fa-solid fa-earth-asia" style="color: var(--epaper-red);"></i> ఇతర జిల్లా & ప్రాంతీయ సంచికలు
        </div>
        <div class="editions-grid">
            @foreach($otherEditions as $oe)
                <a href="{{ route('epaper.reader', ['edition' => $oe, 'pageNumber' => 1]) }}" class="edition-subcard">
                    <div class="edition-subcard-thumb">
                        <img src="{{ $oe->cover_url }}" alt="{{ $oe->title }}" onerror="this.src='https://placehold.co/400x550/0A1D37/FFFFFF/png?text='+encodeURIComponent('{{ $oe->region->name }}');">
                    </div>
                    <div class="edition-subcard-info">
                        <div class="edition-subcard-title">{{ $oe->region->name ?? $oe->title }}</div>
                        <div class="edition-subcard-date"><i class="fa-regular fa-calendar"></i> {{ $oe->edition_date->format('d M Y') }} &bull; {{ $oe->total_pages }} పేజీలు</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

@endsection
