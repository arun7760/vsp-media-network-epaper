<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'te' ? 'ఎడిటోరియల్ లాగిన్' : 'Editorial Desk Login' }} | VSP TIMES E-Paper CMS</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    
    <!-- Unified Modern Typography: Outfit (English) + Noto Sans Telugu (Telugu) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --font-main: {{ app()->getLocale() === 'te' ? "'Noto Sans Telugu', 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" : "'Outfit', 'Noto Sans Telugu', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" }};
        }

        *, *::before, *::after, html, body, button, input, select, textarea, label, h1, h2, h3, h4, h5, h6, p, a, span, div {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font-main) !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .fa, .fas, .far, .fal, .fad, .fab, [class*="fa-"], [class*="fa-"]::before, [class*="fa-"]::after {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }

        body {
            background: radial-gradient(circle at 50% 10%, #002B7A 0%, #00143E 60%, #000B24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #0F172A;
        }

        .login-wrapper {
            width: 100%;
            max-width: 430px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 32px 30px 36px;
            box-shadow: 0 24px 60px rgba(0, 14, 46, 0.45), 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-top: 5px solid #D6001C;
            position: relative;
        }

        /* Language Switcher inside Card */
        .login-lang-switch {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }
        .lang-pill-wrap {
            display: inline-flex;
            background: #F1F5F9;
            border-radius: 999px;
            padding: 3px;
            border: 1px solid #E2E8F0;
            font-size: 12px;
            font-weight: 700;
        }
        .lang-pill-btn {
            padding: 4px 12px;
            border-radius: 999px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            color: #64748B;
            display: inline-block;
            line-height: 1.2;
        }
        .lang-pill-btn.active {
            background: #00205B;
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(0, 32, 91, 0.25);
        }
        .lang-pill-btn:not(.active):hover {
            color: #00205B;
        }

        /* Logo and Branding Header */
        .login-brand-header {
            text-align: center;
            margin-bottom: 22px;
        }
        .login-logo-link {
            display: inline-block;
            text-decoration: none !important;
            outline: none;
            border: none;
            margin-bottom: 12px;
            transition: transform 0.2s ease;
        }
        .login-logo-link:hover {
            transform: scale(1.02);
        }
        .login-logo {
            height: 76px;
            max-height: 80px;
            width: auto;
            max-width: 250px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .login-badge-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            background: #FEE2E2;
            color: #D6001C;
            font-weight: 800;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border: 1px solid #FECACA;
        }

        /* Alerts */
        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }

        /* Form Controls */
        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }
        .form-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #00205B;
            margin-bottom: 7px;
        }
        .form-group label i {
            color: #0047AB;
            font-size: 13px;
        }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #CBD5E1;
            border-radius: 8px;
            font-size: 14px;
            color: #0F172A;
            background-color: #FFFFFF;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            border-color: #0047AB;
            box-shadow: 0 0 0 3px rgba(0, 71, 171, 0.15);
        }
        .form-control::placeholder {
            color: #94A3B8;
        }

        /* Chrome Autofill fix to remove yellow background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #FFFFFF inset !important;
            -webkit-text-fill-color: #0F172A !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 13px;
            color: #64748B;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            user-select: none;
            color: #475569;
            font-weight: 500;
        }
        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #0047AB;
            cursor: pointer;
        }
        .back-link {
            color: #00205B;
            text-decoration: none !important;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s ease, transform 0.2s ease;
        }
        .back-link:hover {
            color: #D6001C;
            transform: translateX(-2px);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #00205B 0%, #0047AB 100%);
            color: #FFFFFF;
            padding: 13px 18px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(0, 32, 91, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #D6001C 0%, #B80018 100%);
            box-shadow: 0 6px 18px rgba(214, 0, 28, 0.4);
            transform: translateY(-1px);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        .login-footer-note {
            text-align: center;
            color: rgba(255, 255, 255, 0.65);
            font-size: 12px;
            font-weight: 500;
        }
        .login-footer-note a {
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 700;
        }
        .login-footer-note a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px 12px;
            }
            .login-card {
                padding: 24px 20px 28px;
                border-radius: 12px;
            }
            .login-logo {
                height: 64px;
            }
            .form-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
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

            <!-- Brand Header: Logo + Badge Centered Vertically -->
            <div class="login-brand-header">
                <a href="{{ route('epaper.home') }}" title="VSP TIMES E-Paper Home" class="login-logo-link">
                    <img src="{{ asset('images/logo.png') }}" alt="VSP TIMES" class="login-logo">
                </a>
                <div class="login-badge-wrap">
                    <span class="login-badge">
                        <i class="fa-solid fa-shield-halved"></i>
                        {{ app()->getLocale() === 'te' ? 'ఎడిటోరియల్ డెస్క్ లాగిన్' : 'Editorial Desk Login' }}
                    </span>
                </div>
            </div>

            @if($errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
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

                <div class="form-meta">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" value="1">
                        {{ app()->getLocale() === 'te' ? 'నన్ను గుర్తుంచుకో' : 'Remember me' }}
                    </label>
                    <a href="{{ route('epaper.home') }}" class="back-link">
                        <i class="fa-solid fa-arrow-left"></i>
                        {{ app()->getLocale() === 'te' ? 'పోర్టల్‌కు వెళ్లండి' : 'Back to E-Paper' }}
                    </a>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    {{ app()->getLocale() === 'te' ? 'డెస్క్‌లోకి ప్రవేశించండి (Login)' : 'Sign In to Dashboard' }}
                </button>
            </form>
        </div>

        <div class="login-footer-note">
            &copy; {{ date('Y') }} VSP Media Network &bull; <a href="{{ route('epaper.home') }}">VSP TIMES E-Paper</a>
        </div>
    </div>

</body>
</html>
