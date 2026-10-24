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
            background: linear-gradient(135deg, #00143E 0%, #00205B 50%, #003380 100%);
            font-family: 'Mandali', 'Noto Sans Telugu', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 42px 38px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 50px rgba(0, 16, 60, 0.45);
            text-align: center;
            border-top: 5px solid #D6001C;
        }
        .login-logo {
            max-height: 80px;
            width: auto;
            max-width: 240px;
            margin-bottom: 16px;
            object-fit: contain;
        }
        .login-badge {
            display: inline-block;
            background: #FEE2E2;
            color: #D6001C;
            font-weight: 800;
            font-size: 11px;
            padding: 4px 12px;
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
            display: block;
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
        <a href="{{ route('epaper.home') }}" title="VSP TIMES E-Paper Home">
            <img src="{{ asset('images/logo.png') }}" alt="VSP TIMES" class="login-logo">
        </a>
        <br>
        <div class="login-badge"><i class="fa-solid fa-lock"></i> ఎడిటోరియల్ డెస్క్ లాగిన్</div>

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
