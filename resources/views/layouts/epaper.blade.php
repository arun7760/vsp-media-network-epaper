<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VSP TIMES ఈ-పేపర్ | VSP Times Telugu E-Paper')</title>
    <meta name="description" content="@yield('meta_description', 'VSP TIMES తెలుగు దినపత్రిక డిజిటల్ ఈ-పేపర్. ప్రతిరోజూ ఉదయం 5 గంటలకే తాజా వార్తలు, ప్రత్యేక కథనాలు, జిల్లా సమాచారం.')">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Best Telugu & International Typography (Ramabhadra + Mandali + Noto Sans Telugu + Outfit) matching vspmedianetwork.com -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Ramabhadra&family=Source+Sans+3:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @include('partials.analytics')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Ramabhadra&family=Source+Sans+3:wght@400;600;700;800;900&display=swap');

        :root {
            --epaper-navy: #0A1D37;
            --epaper-navy-light: #000E24;
            --epaper-blue: #0047AB;
            --epaper-red: #D91B24;
            --epaper-red-dark: #B80F17;
            --epaper-gold: #D91B24;
            --epaper-gold-dark: #B80F17;
            --epaper-bg: #F4F7FB;
            --epaper-card: #FFFFFF;
            --epaper-text: #0F172A;
            --epaper-muted: #64748B;
            --epaper-border: #E2E8F0;
            --font-telugu-body: 'Mandali', 'Noto Sans Telugu', 'Nirmala UI', sans-serif;
            --font-telugu-headline: 'Ramabhadra', 'Noto Sans Telugu', 'Nirmala UI', sans-serif;
            --font-sans: 'Outfit', 'Source Sans 3', system-ui, -apple-system, sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, button, input, select, textarea, p, a, span, div, li {
            font-family: {{ app()->getLocale() === 'en' ? "var(--font-sans)" : "var(--font-telugu-body)" }};
        }

        h1, h2, h3, h4, h5, h6, .brand-title, .live-pulse, .btn-epaper, .region-label, .region-pill, .page-rail-heading, .section-title {
            font-family: {{ app()->getLocale() === 'en' ? "var(--font-sans)" : "var(--font-telugu-headline)" }};
            letter-spacing: {{ app()->getLocale() === 'en' ? "-0.01em" : "0.01em" }};
        }

        .fa, .fas, .far, .fal, .fad, .fab, [class*="fa-"], [class*="fa-"]::before, [class*="fa-"]::after {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }

        body {
            background-color: var(--epaper-bg);
            color: var(--epaper-text);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Utility Bar */
        .epaper-topbar {
            background-color: #00143E;
            color: #94A3B8;
            font-size: 13px;
            padding: 8px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .epaper-topbar .container {
            max-width: 1380px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .live-pulse {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 11.5px;
            background: linear-gradient(135deg, #D6001C 0%, #B80018 100%);
            padding: 3px 12px;
            border-radius: 999px;
            box-shadow: 0 2px 6px rgba(214, 0, 28, 0.4);
        }
        .live-dot {
            width: 7px;
            height: 7px;
            background-color: #FFFFFF;
            border-radius: 50%;
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .topbar-right a {
            color: #CBD5E1;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .topbar-right a:hover {
            color: #FFFFFF;
        }
        .portal-link {
            background: rgba(0, 71, 171, 0.35);
            color: #FFFFFF !important;
            padding: 3px 12px;
            border-radius: 4px;
            font-weight: 700;
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.2s;
        }
        .portal-link:hover {
            background: var(--epaper-red) !important;
        }

        /* Language Switcher in Public Header */
        .locale-switcher {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            padding: 2px 4px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            align-items: center;
            gap: 2px;
        }
        .locale-btn {
            padding: 3px 12px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 999px;
            text-decoration: none;
            color: #CBD5E1;
            transition: all 0.2s;
        }
        .locale-btn.active {
            background: var(--epaper-red);
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(217, 27, 36, 0.45);
        }
        .locale-btn:hover:not(.active) {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.12);
        }

        /* Masthead Primary Language Switcher */
        .masthead-lang-switcher {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            padding: 3px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        }
        .masthead-lang-btn {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 800;
            border-radius: 6px;
            text-decoration: none;
            color: #CBD5E1;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .masthead-lang-btn.active {
            background: #FFFFFF;
            color: var(--epaper-navy) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        }
        .masthead-lang-btn:hover:not(.active) {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.18);
        }

        /* Main Broadsheet Masthead */
        .epaper-masthead {
            background: linear-gradient(180deg, #00143E 0%, #00205B 100%);
            color: #FFFFFF;
            padding: 22px 24px;
            border-bottom: 4px solid var(--epaper-red);
            box-shadow: 0 6px 24px rgba(0, 32, 91, 0.2);
        }
        .epaper-masthead .container {
            max-width: 1380px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .brand-block {
            display: flex;
            align-items: center;
            gap: 18px;
            text-decoration: none;
            color: inherit;
        }
        .brand-logo-wrap {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(0,0,0,0.28);
            border: 1px solid rgba(255,255,255,0.4);
            transition: transform 0.2s ease;
        }
        .brand-logo-wrap:hover {
            transform: translateY(-2px);
        }
        .main-logo-img {
            max-height: 75px;
            width: auto;
            object-fit: contain;
        }
        .brand-text-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--epaper-blue);
            color: #FFFFFF;
            font-family: var(--font-sans);
            font-weight: 800;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 4px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            width: fit-content;
        }
        .brand-tagline {
            font-size: 13px;
            color: #CBD5E1;
            font-weight: 500;
        }

        /* Nav & Action Strip */
        .masthead-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-epaper {
            background: #FFFFFF;
            color: var(--epaper-navy);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .btn-epaper:hover {
            background: var(--epaper-blue);
            color: #FFFFFF;
            transform: translateY(-1px);
        }
        .btn-epaper-gold, .btn-epaper-red {
            background: linear-gradient(135deg, var(--epaper-red) 0%, var(--epaper-red-dark) 100%);
            color: #FFFFFF !important;
            border: none;
            box-shadow: 0 4px 14px rgba(214, 0, 28, 0.4);
        }
        .btn-epaper-gold:hover, .btn-epaper-red:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(214, 0, 28, 0.5);
        }
        .btn-epaper-outline {
            background: rgba(255,255,255,0.08);
            color: #FFFFFF;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .btn-epaper-outline:hover {
            background: rgba(255,255,255,0.18);
            color: #FFFFFF;
            border-color: #FFFFFF;
        }

        /* Main Content Container */
        main {
            flex: 1;
            padding: 30px 20px 60px;
        }
        .content-container {
            max-width: 1380px;
            margin: 0 auto;
        }

        /* Footer */
        .epaper-footer {
            background-color: var(--epaper-navy);
            color: #94A3B8;
            font-size: 14px;
            padding: 32px 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: auto;
        }
        .epaper-footer .container {
            max-width: 1380px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .epaper-footer a {
            color: #CBD5E1;
            text-decoration: none;
            transition: color 0.2s;
        }
        .epaper-footer a:hover {
            color: var(--epaper-gold);
        }

        @media (max-width: 768px) {
            .brand-title {
                font-size: 24px;
            }
            .epaper-masthead {
                padding: 16px;
            }
            .topbar-left, .topbar-right {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('partials.analytics_noscript')

    <!-- Utility Topbar -->
    <header class="epaper-topbar">
        <div class="container">
            <div class="topbar-left">
                <span class="live-pulse"><span class="live-dot"></span> {{ app()->getLocale() === 'en' ? 'Digital Live Edition' : 'డిజిటల్ ఎడిషన్ లైవ్' }}</span>
                <span><i class="fa-regular fa-clock"></i> {{ app()->getLocale() === 'en' ? 'Updated daily at 5:00 AM IST' : 'ప్రతిరోజూ తెల్లవారుజామున 5:00 గంటలకు నవీకరించబడుతుంది' }}</span>
                
                <!-- Live Readership Counter in Header -->
                <span id="header-readership-counter" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.08); padding: 3px 12px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.15); color: #E2E8F0; font-size: 12px; font-weight: 700;">
                    <i class="fa-solid fa-users" style="color: var(--epaper-red);"></i>
                    <span id="header-readership-text">{{ app()->getLocale() === 'en' ? "Today's Readers:" : "ఈ రోజు పాఠకులు:" }}</span>
                    <strong id="header-readership-count" style="color: #FFFFFF;">--</strong>
                </span>
            </div>

            <div class="topbar-right">
                <!-- English / Telugu Language Switcher in Top Bar -->
                <div class="locale-switcher">
                    <a href="{{ route('locale.switch', 'en') }}" class="locale-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                        English
                    </a>
                    <a href="{{ route('locale.switch', 'te') }}" class="locale-btn {{ app()->getLocale() === 'te' ? 'active' : '' }}">
                        తెలుగు
                    </a>
                </div>

                <a href="{{ url('/archive') }}"><i class="fa-solid fa-calendar-days"></i> {{ __('epaper.archive') }}</a>
                <a href="{{ config('app.main_portal_url', 'https://vspmedianetwork.com') }}" class="portal-link" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('epaper.main_news_portal') }}
                </a>
            </div>
        </div>
    </header>

    <!-- Broadsheet Masthead -->
    <div class="epaper-masthead">
        <div class="container">
            <a href="{{ route('epaper.home') }}" class="brand-block">
                <div class="brand-logo-wrap">
                    <img src="{{ asset('images/logo.png') }}" alt="VSP TIMES" class="main-logo-img">
                </div>
            </a>
            <div class="masthead-actions">
                <a href="{{ route('epaper.home') }}" class="btn-epaper btn-epaper-outline">
                    <i class="fa-solid fa-house"></i> {{ __('epaper.home') }}
                </a>
                <a href="{{ route('epaper.archive') }}" class="btn-epaper btn-epaper-outline">
                    <i class="fa-solid fa-box-archive"></i> {{ __('epaper.edition_archive') }}
                </a>
                @if(isset($edition) && $edition)
                    <a href="{{ route('epaper.edition', $edition) }}" class="btn-epaper btn-epaper-red">
                        <i class="fa-solid fa-book-open"></i> {{ __('epaper.read_todays_paper') }}
                    </a>
                @endif

                <!-- Prominent Language Toggle matching Admin Header -->
                <div class="masthead-lang-switcher" title="{{ app()->getLocale() === 'en' ? 'Switch to Telugu' : 'Switch to English' }}">
                    <a href="{{ route('locale.switch', 'en') }}" class="masthead-lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                        English
                    </a>
                    <a href="{{ route('locale.switch', 'te') }}" class="masthead-lang-btn {{ app()->getLocale() === 'te' ? 'active' : '' }}">
                        తెలుగు
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Body -->
    <main>
        <div class="content-container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="epaper-footer">
        <div class="container">
            <div>
                <strong>VSP TIMES E-PAPER</strong> &copy; {{ date('Y') }} VSP Media Network. {{ __('epaper.all_rights_reserved') }}
            </div>
            <div style="display: flex; gap: 20px;">
                <a href="{{ route('epaper.home') }}">{{ __('epaper.home') }}</a>
                <a href="{{ route('epaper.archive') }}">{{ __('epaper.archive') }}</a>
                <a href="{{ config('app.main_portal_url', 'https://vspmedianetwork.com') }}" target="_blank">{{ __('epaper.main_news_portal') }}</a>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <!-- Live Readership Counter Dynamic Refresh -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const counterEl = document.getElementById('header-readership-count');
        if (counterEl) {
            fetch('{{ route('api.readership.today') }}')
                .then(r => r.json())
                .then(data => {
                    if (data && data.success && data.formatted_readers) {
                        counterEl.textContent = data.formatted_readers;
                    }
                })
                .catch(function() {});
        }
    });
    </script>
</body>
</html>
