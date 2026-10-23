<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ఎడిటోరియల్ లాగిన్ | VSP TIMES E-Paper CMS</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mandali&family=Noto+Sans+Telugu:wght@400;500;600;700&family=Ramabhadra&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #071526;
            font-family: 'Mandali', 'Noto Sans Telugu', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 40px 36px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.3);
            text-align: center;
        }
        .login-brand {
            font-family: 'Ramabhadra', sans-serif;
            font-size: 28px;
            color: #0A1D37;
            margin-bottom: 4px;
        }
        .login-brand span { color: #D4AF37; }
        .login-badge {
            display: inline-block;
            background: #D4AF37;
            color: #0A1D37;
            font-weight: 800;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-control:focus {
            border-color: #0A1D37;
            box-shadow: 0 0 0 3px rgba(10,29,55,0.1);
        }
        .btn-submit {
            width: 100%;
            background: #0A1D37;
            color: #FFFFFF;
            padding: 12px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
            margin-top: 8px;
        }
        .btn-submit:hover {
            background: #D4AF37;
            color: #0A1D37;
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
        <div class="login-brand">VSP <span>TIMES</span></div>
        <div class="login-badge">ఈ-పేపర్ ఎడిటోరియల్ డెస్క్ లాగిన్</div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email"><i class="fa-regular fa-envelope"></i> ఈమెయిల్ అడ్రస్</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@vspmedianetwork.com">
            </div>

            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> పాస్‌వర్డ్</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px; color: #64748B;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="remember" value="1"> నన్ను గుర్తుంచుకో
                </label>
                <a href="{{ route('epaper.home') }}" style="color: #0A1D37; text-decoration: none; font-weight: 600;">పోర్టల్‌కు వెళ్లండి</a>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i> డెస్క్‌లోకి ప్రవేశించండి (Login)
            </button>
        </form>
    </div>

</body>
</html>
