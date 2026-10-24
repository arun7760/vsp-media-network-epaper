<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('admin.portal_title'))</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Fonts matching main website (vspmedianetwork.com) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Ramabhadra&family=Source+Sans+3:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @include('partials.analytics')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Ramabhadra&family=Source+Sans+3:wght@400;600;700;800;900&display=swap');

        :root {
            --admin-navy: #0A1D37;
            --admin-navy-dark: #000E24;
            --admin-blue: #0047AB;
            --admin-red: #D91B24;
            --admin-red-hover: #B80F17;
            --admin-red-light: #FEE2E2;
            --admin-gold: #D91B24;
            --admin-bg: #F4F7FB;
            --admin-card: #FFFFFF;
            --admin-border: #E2E8F0;
            --admin-text: #0F172A;
            --admin-muted: #64748B;
            --font-telugu: 'Mandali', 'Noto Sans Telugu', 'Nirmala UI', sans-serif;
            --font-headline: 'Ramabhadra', 'Noto Sans Telugu', 'Nirmala UI', sans-serif;
            --font-sans: 'Outfit', 'Source Sans 3', system-ui, sans-serif;
        }

        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        body, button, input, select, textarea, h1, h2, h3, h4, h5, h6, p, a, span, div {
            font-family: {{ app()->getLocale() === 'en' ? "var(--font-sans)" : "var(--font-telugu)" }};
        }

        h1, h2, h3, h4, h5, h6, .brand-title {
            font-family: {{ app()->getLocale() === 'en' ? "var(--font-sans)" : "var(--font-headline)" }};
        }

        .fa, .fas, .far, .fal, .fad, .fab, [class*="fa-"], [class*="fa-"]::before, [class*="fa-"]::after {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }

        body {
            background-color: var(--admin-bg);
            color: var(--admin-text);
            display: flex;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 275px;
            background: linear-gradient(180deg, #061124 0%, #0A1D37 60%, #050E1C 100%);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            border-right: 1px solid rgba(255,255,255,0.08);
            box-shadow: 4px 0 24px rgba(6, 17, 36, 0.35);
        }
        .sidebar-brand {
            padding: 22px 18px 18px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0, 0, 0, 0.18);
        }
        .brand-logo-card {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.4);
            transition: all 0.25s ease;
        }
        .brand-logo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.45);
        }
        .brand-logo-img {
            max-height: 48px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
        }
        .brand-meta-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 10px;
            font-size: 10.5px;
            letter-spacing: 0.8px;
            font-weight: 800;
            color: #94A3B8;
            text-transform: uppercase;
        }
        .live-dot-pulse {
            width: 7px;
            height: 7px;
            background: #10B981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10B981;
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        .sidebar-nav {
            padding: 18px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 5px;
            overflow-y: auto;
        }
        .nav-heading {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748B;
            font-weight: 800;
            padding: 14px 12px 6px;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
        }
        .nav-link {
            padding: 10px 14px;
            border-radius: 9px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #FFFFFF;
            transform: translateX(3px);
        }
        .nav-link.active {
            background: linear-gradient(135deg, #D91B24 0%, #A90D15 100%);
            color: #FFFFFF;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(217, 27, 36, 0.4);
        }
        .nav-link .badge {
            margin-left: auto;
            background: #FFFFFF;
            color: #D91B24;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 999px;
            font-weight: 800;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .sidebar-footer {
            padding: 16px 18px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(0, 0, 0, 0.35);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.05);
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--admin-red) 0%, var(--admin-blue) 100%);
            color: #FFFFFF;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1.2;
        }
        .user-role {
            font-size: 11px;
            color: #94A3B8;
            text-transform: capitalize;
            margin-top: 1px;
        }

        /* Main Workspace */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .admin-header {
            background: #FFFFFF;
            border-bottom: 2px solid #E2E8F0;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 32, 91, 0.04);
        }
        .admin-header h2 {
            font-family: 'Ramabhadra', sans-serif;
            font-size: 20px;
            color: var(--admin-navy);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-header h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 20px;
            background: var(--admin-red);
            border-radius: 2px;
        }

        .admin-content {
            padding: 32px;
            flex: 1;
        }

        /* Alerts & Flash Messages */
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; }
        .alert-error { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
        .alert-warning { background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; }
        .alert-info { background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; }

        /* General UI Elements */
        .btn-admin {
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--admin-blue) 0%, var(--admin-navy) 100%); 
            color: #FFFFFF; 
            box-shadow: 0 2px 8px rgba(0, 71, 171, 0.25);
        }
        .btn-primary:hover { 
            filter: brightness(1.1); 
            box-shadow: 0 4px 14px rgba(0, 71, 171, 0.4); 
            color: #FFFFFF;
        }
        .btn-gold, .btn-brand { 
            background: linear-gradient(135deg, var(--admin-red) 0%, var(--admin-red-hover) 100%); 
            color: #FFFFFF; 
            box-shadow: 0 2px 8px rgba(214, 0, 28, 0.3);
        }
        .btn-gold:hover, .btn-brand:hover { 
            filter: brightness(1.1); 
            box-shadow: 0 4px 14px rgba(214, 0, 28, 0.45); 
            color: #FFFFFF;
        }
        .btn-outline { 
            background: #FFFFFF; 
            color: var(--admin-navy); 
            border: 1px solid var(--admin-border); 
        }
        .btn-outline:hover { 
            background: #F1F5F9; 
            color: var(--admin-blue);
            border-color: var(--admin-blue);
        }
        .btn-danger { 
            background: var(--admin-red); 
            color: #FFFFFF; 
        }
        .btn-danger:hover { 
            background: var(--admin-red-hover); 
        }
        .btn-success { 
            background: #10B981; 
            color: #FFFFFF; 
        }

        .card {
            background: #FFFFFF;
            border: 1px solid var(--admin-border);
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            margin-bottom: 28px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div class="brand-logo-card">
                <img src="{{ asset('images/logo.png') }}" alt="VSP TIMES" class="brand-logo-img">
            </div>
            <div class="brand-meta-badge">
                <span class="live-dot-pulse"></span>
                <span>EDITORIAL CONTROL SUITE</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-heading">{{ __('admin.nav_management') }}</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> {{ __('admin.dashboard') }}
            </a>

            @if(auth()->user()->canUpload())
                <a href="{{ route('admin.editions.create') }}" class="nav-link {{ request()->routeIs('admin.editions.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('admin.upload_edition') }}
                </a>
            @endif

            <a href="{{ route('admin.editions.index') }}" class="nav-link {{ request()->routeIs('admin.editions.index') && !request('status') ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper"></i> {{ __('admin.all_editions') }}
            </a>

            <div class="nav-heading">{{ __('admin.nav_workflows') }}</div>
            <a href="{{ route('admin.editions.index', ['status' => 'pending_approval']) }}" class="nav-link {{ request('status') === 'pending_approval' ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i> {{ __('admin.pending_approvals') }}
                @php $pendingCount = \App\Models\EpaperEdition::where('status', 'pending_approval')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.editions.index', ['status' => 'scheduled']) }}" class="nav-link {{ request('status') === 'scheduled' ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i> {{ __('admin.scheduled_editions') }}
            </a>

            <a href="{{ route('admin.editions.index', ['status' => 'published']) }}" class="nav-link {{ request('status') === 'published' ? 'active' : '' }}">
                <i class="fa-solid fa-circle-check"></i> {{ __('admin.published_editions') }}
            </a>

            @if(auth()->user()->isAdmin())
                <div class="nav-heading">{{ __('admin.nav_administration') }}</div>
                <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> {{ __('admin.analytics') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i> {{ __('admin.users_roles') }}
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> {{ __('admin.audit_logs') }}
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> {{ __('admin.settings') }}
                </a>
            @endif

            <div class="nav-heading">{{ __('admin.nav_public') }}</div>
            <a href="{{ route('epaper.home') }}" target="_blank" class="nav-link">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('admin.view_live_epaper') }}
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-admin btn-outline" style="width: 100%; justify-content: center; background: rgba(255,255,255,0.06); color: #CBD5E1; border-color: rgba(255,255,255,0.15);">
                    <i class="fa-solid fa-right-from-bracket"></i> {{ __('admin.logout') }}
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <header class="admin-header">
            <div>
                <div style="font-size: 12px; color: var(--admin-muted); display: flex; align-items: center; gap: 6px; margin-bottom: 3px; font-weight: 600;">
                    <i class="fa-solid fa-house" style="font-size: 11px;"></i>
                    <span>{{ __('admin.dashboard') }}</span>
                    <span style="color: #CBD5E1;">/</span>
                    <span style="color: var(--admin-navy);">{{ __('admin.portal_title') }}</span>
                </div>
                <h2 style="font-size: 22px; font-weight: 800; color: var(--admin-navy); margin: 0; letter-spacing: -0.3px;">
                    @yield('header_title', __('admin.portal_title'))
                </h2>
            </div>
            
            <div style="display: flex; align-items: center; gap: 16px;">
                <!-- System Status Indicator -->
                <div style="display: flex; align-items: center; gap: 7px; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; color: #065F46;">
                    <span class="live-dot-pulse"></span>
                    <span>5:00 AM IST Auto-Publish Active</span>
                </div>

                <!-- English / Telugu Language Segmented Control -->
                <div style="display: flex; background: #F1F5F9; border-radius: 8px; padding: 3px; border: 1px solid var(--admin-border); box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);">
                    <a href="{{ route('admin.locale.switch', 'en') }}" 
                       style="padding: 5px 14px; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ app()->getLocale() === 'en' ? '#FFFFFF' : '#64748B' }}; background: {{ app()->getLocale() === 'en' ? '#0F172A' : 'transparent' }}; box-shadow: {{ app()->getLocale() === 'en' ? '0 2px 6px rgba(0,0,0,0.18)' : 'none' }}; transition: all 0.2s;">
                        English
                    </a>
                    <a href="{{ route('admin.locale.switch', 'te') }}" 
                       style="padding: 5px 14px; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none; color: {{ app()->getLocale() === 'te' ? '#FFFFFF' : '#64748B' }}; background: {{ app()->getLocale() === 'te' ? '#0F172A' : 'transparent' }}; box-shadow: {{ app()->getLocale() === 'te' ? '0 2px 6px rgba(0,0,0,0.18)' : 'none' }}; transition: all 0.2s;">
                        తెలుగు
                    </a>
                </div>

                <!-- View Live E-Paper Button -->
                <a href="{{ route('epaper.home') }}" target="_blank" 
                   style="background: #FFFFFF; color: var(--admin-navy); border: 1px solid var(--admin-border); font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px; padding: 7px 16px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); transition: all 0.2s;">
                    <i class="fa-solid fa-arrow-up-right-from-square" style="color: var(--admin-red);"></i>
                    <span>{{ __('admin.view_live_website') }}</span>
                </a>

                <!-- Logout Button -->
                <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.2); color: #DC2626; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; transition: all 0.2s;">
                        <i class="fa-solid fa-power-off"></i>
                        <span>{{ __('admin.logout') }}</span>
                    </button>
                </form>

                <div style="font-size: 12px; color: var(--admin-muted); border-left: 1px solid var(--admin-border); padding-left: 14px; font-weight: 600;">
                    <i class="fa-regular fa-clock" style="color: var(--admin-blue); margin-right: 4px;"></i> {{ now('Asia/Kolkata')->format('d M Y, h:i A') }} IST
                </div>
            </div>
        </header>

        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fa-solid fa-circle-info"></i> {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        <strong>దోషం సంభవించింది:</strong>
                        <ul style="margin-left: 18px; margin-top: 4px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
