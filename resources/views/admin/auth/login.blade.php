<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'te' ? 'ఎడిటోరియల్ లాగిన్' : 'Editorial Desk Login' }} | VSP TIMES E-Paper CMS</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Source+Sans+3:wght@400;600;700&family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: linear-gradient(135deg, #00143E 0%, #00205B 50%, #003380 100%);
            font-family: {{ app()->getLocale() === 'te' ? "'Noto Sans Telugu', 'Mandali', sans-serif" : "'Outfit', 'Source Sans 3', system-ui, sans-serif" }};
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 36px 34px 42px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 50px rgba(0, 16, 60, 0.45);
            text-align: center;
            border-top: 5px solid #D6001C;
            position: relative;
        }
        .login-lang-switch {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 12px;
        }
        .lang-pill-wrap {
            display: inline-flex;
            background: #F1F5F9;
            border-radius: 20px;
            padding: 2px;
            border: 1px solid #E2E8F0;
            font-size: 11px;
            font-weight: 700;
        }
        .lang-pill-btn {
            padding: 4px 12px;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .lang-pill-btn.active {
            background: #00205B;
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(0, 32, 91, 0.25);
        }
        .lang-pill-btn:not(.active) {
            color: #64748B;
        }
        .lang-pill-btn:not(.active):hover {
            color: #00205B;
        }
        .login-logo {
            height: 75px;
            max-height: 80px;
            width: auto;
            max-width: 240px;
            margin-bottom: 14px;
            object-fit: contain;
        }
        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #FEE2E2;
            color: #D6001C;
            font-weight: 800;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
            border: 1px solid #FECACA;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .form-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #00205B;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }
        .form-control:focus {
            border-color: #0047AB;
            box-shadow: 0 0 0 3px rgba(0, 71, 171, 0.15);
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #0047AB 0%, #00205B 100%);
            color: #FFFFFF;
            padding: 13px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(0, 71, 171, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #D6001C 0%, #B80018 100%);
            box-shadow: 0 4px 15px rgba(214, 0, 28, 0.4);
            transform: translateY(-1px);
        }
        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- English / Telugu Language Switcher -->
        <div class="login-lang-switch">
            <div class="lang-pill-wrap">
                <a href="{{ route('locale.switch', 'en') }}" class="lang-pill-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                    English
                </a>
                <a href="{{ route('locale.switch', 'te') }}" class="lang-pill-btn {{ app()->getLocale() === 'te' ? 'active' : '' }}">
                    తెలుగు
                </a>
            </div>
        </div>

        <a href="{{ route('epaper.home') }}" title="VSP TIMES E-Paper Home">
            <img src="{{ asset('images/logo.png') }}" alt="VSP TIMES" class="login-logo">
        </a>

        <div class="login-badge">
            <i class="fa-solid fa-shield-halved"></i>
            {{ app()->getLocale() === 'te' ? 'ఎడిటోరియల్ డెస్క్ లాగిన్' : 'Editorial Desk Login' }}
        </div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">
                    <i class="fa-regular fa-envelope"></i>
                    {{ app()->getLocale() === 'te' ? 'ఈమెయిల్ అడ్రస్' : 'Email Address' }}
                </label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@vspmedianetwork.com">
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fa-solid fa-lock"></i>
                    {{ app()->getLocale() === 'te' ? 'పాస్‌వర్డ్' : 'Password' }}
                </label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px; color: #64748B;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="remember" value="1">
                    {{ app()->getLocale() === 'te' ? 'నన్ను గుర్తుంచుకో' : 'Remember me' }}
                </label>
                <a href="{{ route('epaper.home') }}" style="color: #0A1D37; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-arrow-left" style="font-size: 11px;"></i>
                    {{ app()->getLocale() === 'te' ? 'పోర్టల్‌కు వెళ్లండి' : 'Back to E-Paper' }}
                </a>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i>
                {{ app()->getLocale() === 'te' ? 'డెస్క్‌లోకి ప్రవేశించండి (Login)' : 'Sign In to Dashboard' }}
            </button>
        </form>
    </div>

</body>
</html>
