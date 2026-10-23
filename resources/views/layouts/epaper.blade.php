<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VSP TIMES ఈ-పేపర్ | VSP Times Telugu E-Paper')</title>
    <meta name="description" content="@yield('meta_description', 'VSP TIMES తెలుగు దినపత్రిక డిజిటల్ ఈ-పేపర్. ప్రతిరోజూ ఉదయం 5 గంటలకే తాజా వార్తలు, ప్రత్యేక కథనాలు, జిల్లా సమాచారం.')">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Telugu & International Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700;800&family=Ramabhadra&family=Source+Sans+3:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --epaper-navy: #0A1D37;
            --epaper-navy-light: #132A4B;
            --epaper-gold: #D4AF37;
            --epaper-gold-dark: #B8860B;
            --epaper-red: #D32F2F;
            --epaper-bg: #F4F6F9;
            --epaper-card: #FFFFFF;
            --epaper-text: #1E293B;
            --epaper-muted: #64748B;
            --epaper-border: #E2E8F0;
            --font-telugu-serif: 'Ramabhadra', 'Noto Sans Telugu', serif;
            --font-telugu-sans: 'Mandali', 'Noto Sans Telugu', sans-serif;
            --font-sans: 'Source Sans 3', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--epaper-bg);
            color: var(--epaper-text);
            font-family: var(--font-telugu-sans);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Utility Bar */
        .epaper-topbar {
            background-color: #071526;
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
            color: #10B981;
            font-weight: 600;
            font-size: 12px;
            background: rgba(16, 185, 129, 0.1);
            padding: 3px 10px;
            border-radius: 999px;
        }
        .live-dot {
            width: 7px;
            height: 7px;
            background-color: #10B981;
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
            color: var(--epaper-gold);
        }
        .portal-link {
            background: rgba(212, 175, 55, 0.15);
            color: var(--epaper-gold) !important;
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Main Broadsheet Masthead */
        .epaper-masthead {
            background: linear-gradient(180deg, var(--epaper-navy) 0%, var(--epaper-navy-light) 100%);
            color: #FFFFFF;
            padding: 24px 24px;
            border-bottom: 3px solid var(--epaper-gold);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
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
            gap: 16px;
            text-decoration: none;
            color: inherit;
        }
        .brand-title {
            font-family: var(--font-telugu-serif);
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.1;
            color: #FFFFFF;
        }
        .brand-title span {
            color: var(--epaper-gold);
        }
        .brand-badge {
            display: inline-block;
            background: var(--epaper-gold);
            color: var(--epaper-navy);
            font-family: var(--font-sans);
            font-weight: 900;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 3px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .brand-tagline {
            font-size: 12px;
            color: #94A3B8;
            margin-top: 2px;
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
            padding: 9px 18px;
            border-radius: 6px;
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
            background: var(--epaper-gold);
            color: var(--epaper-navy);
            transform: translateY(-1px);
        }
        .btn-epaper-gold {
            background: linear-gradient(135deg, #F3C644 0%, #D4AF37 100%);
            color: var(--epaper-navy);
            border: none;
        }
        .btn-epaper-gold:hover {
            filter: brightness(1.08);
        }
        .btn-epaper-outline {
            background: transparent;
            color: #FFFFFF;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .btn-epaper-outline:hover {
            background: rgba(255,255,255,0.1);
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

    <!-- Utility Topbar -->
    <header class="epaper-topbar">
        <div class="container">
            <div class="topbar-left">
                <span class="live-pulse"><span class="live-dot"></span> డిజిటల్ ఎడిషన్ లైవ్</span>
                <span><i class="fa-regular fa-clock"></i> ప్రతిరోజూ తెల్లవారుజామున 5:00 గంటలకు నవీకరించబడుతుంది</span>
            </div>
            <div class="topbar-right">
                <a href="{{ url('/archive') }}"><i class="fa-solid fa-calendar-days"></i> పాత సంచికలు (Archive)</a>
                <a href="{{ config('app.main_portal_url', 'https://vspmedianetwork.com') }}" class="portal-link" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> ప్రధాన వార్తా పోర్టల్
                </a>
            </div>
        </div>
    </header>

    <!-- Broadsheet Masthead -->
    <div class="epaper-masthead">
        <div class="container">
            <a href="{{ route('epaper.home') }}" class="brand-block">
                <div>
                    <div class="brand-title">VSP <span>TIMES</span> ఈ-పేపర్</div>
                    <div class="brand-badge">Digital Newspaper Reader</div>
                    <div class="brand-tagline">తెలుగు రాష్ట్రాల సమగ్ర డిజిటల్ దినపత్రిక</div>
                </div>
            </a>
            <div class="masthead-actions">
                <a href="{{ route('epaper.home') }}" class="btn-epaper btn-epaper-outline">
                    <i class="fa-solid fa-house"></i> హోమ్
                </a>
                <a href="{{ route('epaper.archive') }}" class="btn-epaper btn-epaper-outline">
                    <i class="fa-solid fa-box-archive"></i> సంచికల ఆర్కైవ్
                </a>
                @if(isset($edition) && $edition)
                    <a href="{{ route('epaper.edition', $edition) }}" class="btn-epaper btn-epaper-gold">
                        <i class="fa-solid fa-book-open"></i> నేటి పేపర్ చదవండి
                    </a>
                @endif
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
                <strong>VSP TIMES E-PAPER</strong> &copy; {{ date('Y') }} VSP Media Network. All rights reserved.
            </div>
            <div style="display: flex; gap: 20px;">
                <a href="{{ route('epaper.home') }}">హోమ్</a>
                <a href="{{ route('epaper.archive') }}">ఆర్కైవ్</a>
                <a href="{{ config('app.main_portal_url', 'https://vspmedianetwork.com') }}" target="_blank">మెయిన్ న్యూస్ వెబ్‌సైట్</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
