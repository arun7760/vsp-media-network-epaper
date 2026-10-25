@extends('layouts.epaper')

@section('title', 'ఈ-పేపర్ ఆర్కైవ్ | పాత సంచికలు | VSP TIMES E-Paper Archive')

@push('styles')
<style>
    .archive-header {
        background: #FFFFFF;
        padding: 24px 30px;
        border-radius: 12px;
        border: 1px solid var(--epaper-border);
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 32px;
    }
    .archive-title-block h1 {
        font-size: 26px;
        font-weight: 800;
        color: var(--epaper-navy);
        margin-bottom: 6px;
    }
    .archive-title-block p {
        color: var(--epaper-muted);
        font-size: 14px;
        margin-bottom: 20px;
    }
    .filter-form {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: center;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-group label {
        font-weight: 700;
        font-size: 13px;
        color: var(--epaper-navy);
    }
    .filter-select {
        padding: 8px 14px;
        border-radius: 6px;
        border: 1px solid var(--epaper-border);
        background: #F8FAFC;
        font-size: 14px;
        font-weight: 600;
        color: var(--epaper-text);
        outline: none;
    }
    .filter-select:focus {
        border-color: var(--epaper-blue);
        background: #FFFFFF;
        box-shadow: 0 0 0 3px rgba(0, 71, 171, 0.15);
    }
    .btn-filter-submit {
        background: linear-gradient(135deg, var(--epaper-navy) 0%, var(--epaper-blue) 100%);
        color: #FFFFFF;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-filter-submit:hover {
        background: linear-gradient(135deg, var(--epaper-red) 0%, var(--epaper-red-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(214, 0, 28, 0.3);
    }

    /* Archive Grid */
    .archive-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 28px;
        margin-bottom: 40px;
    }
    .archive-card {
        background: #FFFFFF;
        border: 1px solid var(--epaper-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }
    .archive-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(10,29,55,0.1);
    }
    .archive-cover-wrap {
        background: #F8FAFC;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid var(--epaper-border);
    }
    .archive-cover-wrap img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: contain;
    }
    .archive-card-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .archive-region-tag {
        font-size: 12px;
        font-weight: 800;
        color: var(--epaper-red);
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }
    .archive-edition-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--epaper-navy);
        line-height: 1.3;
        margin-bottom: 8px;
    }
    .archive-date-info {
        font-size: 13px;
        color: var(--epaper-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 16px;
    }
    .archive-card-actions {
        margin-top: auto;
        display: flex;
        gap: 10px;
    }
    .btn-card-read {
        flex: 1;
        background: var(--epaper-navy);
        color: #FFFFFF;
        text-align: center;
        padding: 9px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-card-read:hover {
        background: linear-gradient(135deg, var(--epaper-red) 0%, var(--epaper-red-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 10px rgba(214, 0, 28, 0.3);
    }
    .btn-card-pdf {
        background: #F8FAFC;
        color: var(--epaper-navy);
        border: 1px solid var(--epaper-border);
        padding: 9px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    .btn-card-pdf:hover {
        background: #F1F5F9;
        color: var(--epaper-red);
    }
</style>
@endpush

@section('content')

    <!-- Archive Filter Bar -->
    <div class="archive-header">
        <div class="archive-title-block">
            <h1><i class="fa-solid fa-box-archive" style="color: var(--epaper-red);"></i> VSP TIMES ఈ-పేపర్ ఆర్కైవ్</h1>
            <p>గత సంచికలను తేదీ మరియు ప్రాంతీయ ఎడిషన్ల వారీగా సులభంగా వెతకండి మరియు డిజిటల్ ఫార్మాట్‌లో చదవండి.</p>
        </div>

        <form action="{{ route('epaper.archive') }}" method="GET" class="filter-form">
            @if($regions->count() > 1)
                <!-- Region Filter -->
                <div class="filter-group">
                    <label for="region_id">{{ __('epaper.select_edition') }}</label>
                    <select name="region_id" id="region_id" class="filter-select">
                        <option value="">{{ __('epaper.all_editions') }}</option>
                        @foreach($regions as $r)
                            <option value="{{ $r->id }}" {{ $selectedRegionId == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Month Filter -->
            <div class="filter-group">
                <label for="month"><i class="fa-regular fa-calendar"></i> నెల:</label>
                <select name="month" id="month" class="filter-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Year Filter -->
            <div class="filter-group">
                <label for="year"><i class="fa-solid fa-calendar-check"></i> సంవత్సరం:</label>
                <select name="year" id="year" class="filter-select">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="btn-filter-submit">
                <i class="fa-solid fa-magnifying-glass"></i> శోధించండి
            </button>
        </form>
    </div>

    <!-- Editions Grid -->
    @if($editions->isNotEmpty())
        <div class="archive-grid">
            @foreach($editions as $ed)
                <div class="archive-card">
                    <div class="archive-cover-wrap">
                        <img src="{{ $ed->cover_url }}" alt="{{ $ed->title }}" onerror="this.src='/images/epaper-default-cover.jpg';">
                    </div>
                    <div class="archive-card-body">
                        <div class="archive-region-tag">{{ $ed->region->name ?? 'ప్రధాన సంచిక' }}</div>
                        <h2 class="archive-edition-title">{{ $ed->title }}</h2>
                        <div class="archive-date-info">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ $ed->edition_date->format('d M Y') }} &bull; {{ $ed->total_pages }} పేజీలు</span>
                        </div>
                        <div class="archive-card-actions">
                            <a href="{{ route('epaper.reader', ['edition' => $ed, 'pageNumber' => 1]) }}" class="btn-card-read">
                                <i class="fa-solid fa-book-open"></i> చదవండి
                            </a>
                            <a href="{{ route('epaper.download', $ed) }}" class="btn-card-pdf" title="పీడీఎఫ్ డౌన్‌లోడ్">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 24px;">
            {{ $editions->links() }}
        </div>
    @else
        <div style="background: #FFFFFF; padding: 48px; border-radius: 12px; text-align: center; border: 1px solid var(--epaper-border);">
            <i class="fa-solid fa-calendar-xmark" style="font-size: 40px; color: var(--epaper-muted); margin-bottom: 14px;"></i>
            <h3 style="font-size: 20px; color: var(--epaper-navy); margin-bottom: 6px;">ఎంచుకున్న నెలకు ఎటువంటి సంచికలు లభించలేదు</h3>
            <p style="color: var(--epaper-muted);">దయచేసి వేరొక నెల లేదా సంవత్సరాన్ని ఎంచుకోండి.</p>
        </div>
    @endif

@endsection
