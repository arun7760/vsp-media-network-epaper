<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VSP TIMES ఈ-పేపర్ ఎడిటోరియల్ డెస్క్')</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700;800&family=Ramabhadra&family=Source+Sans+3:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --admin-navy: #0A1D37;
            --admin-navy-dark: #071526;
            --admin-gold: #D4AF37;
            --admin-bg: #F8FAFC;
            --admin-card: #FFFFFF;
            --admin-border: #E2E8F0;
            --admin-text: #1E293B;
            --admin-muted: #64748B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--admin-bg);
            color: var(--admin-text);
            font-family: 'Mandali', 'Noto Sans Telugu', sans-serif;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 270px;
            background: var(--admin-navy);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            border-right: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-decoration: none;
            color: #FFFFFF;
            display: block;
        }
        .sidebar-brand h1 {
            font-family: 'Ramabhadra', sans-serif;
            font-size: 22px;
            line-height: 1.1;
        }
        .sidebar-brand span { color: var(--admin-gold); }
        .sidebar-badge {
            display: inline-block;
            background: var(--admin-gold);
            color: var(--admin-navy);
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .sidebar-nav {
            padding: 20px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nav-heading {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748B;
            font-weight: 800;
            padding: 10px 12px 4px;
            letter-spacing: 0.5px;
        }
        .nav-link {
            padding: 10px 14px;
            border-radius: 6px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.15s;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #FFFFFF;
        }
        .nav-link.active {
            background: var(--admin-gold);
            color: var(--admin-navy);
            font-weight: 700;
        }
        .nav-link .badge {
            margin-left: auto;
            background: #D32F2F;
            color: #FFFFFF;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 999px;
            font-weight: 800;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: var(--admin-navy-dark);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--admin-gold);
            color: var(--admin-navy);
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
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
            border-bottom: 1px solid var(--admin-border);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .admin-header h2 {
            font-family: 'Ramabhadra', sans-serif;
            font-size: 20px;
            color: var(--admin-navy);
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
            transition: all 0.15s;
        }
        .btn-primary { background: var(--admin-navy); color: #FFFFFF; }
        .btn-primary:hover { background: #132A4B; }
        .btn-gold { background: var(--admin-gold); color: var(--admin-navy); }
        .btn-gold:hover { filter: brightness(1.05); }
        .btn-outline { background: #FFFFFF; color: var(--admin-navy); border: 1px solid var(--admin-border); }
        .btn-outline:hover { background: #F8FAFC; }
        .btn-danger { background: #DC2626; color: #FFFFFF; }
        .btn-danger:hover { background: #B91C1C; }
        .btn-success { background: #10B981; color: #FFFFFF; }

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
            <h1>VSP <span>TIMES</span></h1>
            <span class="sidebar-badge">E-Paper Editorial Desk</span>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-heading">నిర్వహణ (Management)</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> డ్యాష్‌బోర్డ్
            </a>

            @if(auth()->user()->canUpload())
                <a href="{{ route('admin.editions.create') }}" class="nav-link {{ request()->routeIs('admin.editions.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-cloud-arrow-up"></i> కొత్త సంచిక అప్‌లోడ్
                </a>
            @endif

            <a href="{{ route('admin.editions.index') }}" class="nav-link {{ request()->routeIs('admin.editions.index') && !request('status') ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper"></i> అన్ని సంచికలు
            </a>

            <div class="nav-heading">పనితీరు &amp; ఆమోదాలు (Workflows)</div>
            <a href="{{ route('admin.editions.index', ['status' => 'pending_approval']) }}" class="nav-link {{ request('status') === 'pending_approval' ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i> ఆమోదం కోసం వేచిఉన్నవి
                @php $pendingCount = \App\Models\EpaperEdition::where('status', 'pending_approval')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.editions.index', ['status' => 'scheduled']) }}" class="nav-link {{ request('status') === 'scheduled' ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-clock"></i> షెడ్యూల్డ్ సంచికలు (5:00 AM)
            </a>

            <a href="{{ route('admin.editions.index', ['status' => 'published']) }}" class="nav-link {{ request('status') === 'published' ? 'active' : '' }}">
                <i class="fa-solid fa-circle-check"></i> ప్రచురితమైనవి (Live)
            </a>

            <div class="nav-heading">పబ్లిక్ పోర్టల్ (Public)</div>
            <a href="{{ route('epaper.home') }}" target="_blank" class="nav-link">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> లైవ్ ఈ-పేపర్ చూడండి
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
                    <i class="fa-solid fa-right-from-bracket"></i> లాగౌట్ (Logout)
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <header class="admin-header">
            <h2>@yield('header_title', 'ఈ-పేపర్ ఎడిటోరియల్ డెస్క్')</h2>
            <div style="font-size: 13px; color: var(--admin-muted);">
                <i class="fa-regular fa-clock"></i> {{ now('Asia/Kolkata')->format('d M Y, h:i A') }} IST
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
