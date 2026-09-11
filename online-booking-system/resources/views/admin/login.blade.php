<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CASAUL Hotel - Staff Login</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        :root {
            --burgundy: #6e1f2b;
            --burgundy-dark: #4d1420;
            --cream: #f8f4ef;
            --ink: #2b2322;
            --muted: #817675;
            --line: #e7ddda;
        }
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body {
            background: #fbf7f5;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }
        .login-card {
            display: grid;
            grid-template-columns: minmax(280px, 0.86fr) minmax(420px, 1.14fr);
            background: #fff;
            border: 1px solid rgba(110, 31, 43, 0.12);
            border-radius: 18px;
            width: 100%;
            max-width: 945px;
            min-height: 680px;
            overflow: hidden;
            box-shadow: 0 18px 55px rgba(77, 20, 32, 0.14);
        }
        .brand-panel {
            position: relative;
            isolation: isolate;
            background: linear-gradient(180deg, #742733 0%, #5c1b26 100%);
            color: #fff;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 50px 36px 260px;
            text-align: center;
            overflow: hidden;
        }
        .brand-panel::before { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 45%, rgba(77, 20, 32, 0.58) 100%); z-index: -1; }
        .brand-content { position: relative; z-index: 2; max-width: 290px; }
        .brand-mark { width: 94px; height: 94px; object-fit: contain; margin: 0 auto 20px; }
        .brand-panel h1 { margin: 0; font-size: 31px; font-weight: 700; letter-spacing: 2.5px; }
        .brand-panel p { color: #f0dfe0; font-size: 13px; font-style: italic; line-height: 1.7; margin: 18px 0 0; }
        .brand-caption { display: block; color: #e5c5c8; font-size: 9px; font-style: normal; letter-spacing: 3px; margin-top: 18px; }
        .brand-photo { position: absolute; z-index: 1; right: 0; bottom: 0; left: 0; width: 100%; height: 38%; object-fit: cover; opacity: 0.78; mix-blend-mode: screen; }
        .form-panel { display: flex; align-items: center; padding: 64px clamp(38px, 7vw, 72px); background: #fffdfc; }
        .form-content { width: 100%; max-width: 434px; margin: 0 auto; }
        .form-heading { margin-bottom: 38px; }
        .form-heading::before { content: 'STAFF PORTAL'; display: block; color: #b26a71; font-size: 12px; font-weight: 600; letter-spacing: 2px; margin-bottom: 10px; }
        .form-heading h2 { color: #292a38; font-size: 39px; line-height: 1.15; font-weight: 700; margin: 0; letter-spacing: -0.5px; }
        .form-heading p { color: #8690a3; font-size: 16px; margin: 14px 0 0; }
        .form-group { margin-bottom: 19px; }
        .form-group label { display: block; position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 17px; top: 50%; transform: translateY(-50%); color: #7d8798; font-size: 16px; z-index: 1; }
        .input-wrap input,
        .input-wrap select {
            width: 100%;
            height: 58px;
            padding: 0 16px 0 54px;
            border: 1px solid #d7dce3;
            border-radius: 10px;
            color: var(--ink);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            background: #fff;
        }
        .input-wrap input::placeholder { color: #909aab; opacity: 1; }
        .input-wrap select { color: #909aab; }
        .input-wrap input:focus,
        .input-wrap select:focus {
            border-color: var(--burgundy);
            box-shadow: 0 0 0 3px rgba(110, 31, 43, 0.1);
            background: #fff;
        }
        .remember-group { display: flex; align-items: center; gap: 9px; margin: 3px 0 27px; }
        .remember-group input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--burgundy); }
        .remember-group label { position: static; width: auto; height: auto; overflow: visible; clip: auto; color: #707b8d; font-size: 13px; }
        .login-btn {
            width: 100%;
            height: 54px;
            background: var(--burgundy);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .login-btn:hover { background: var(--burgundy-dark); box-shadow: 0 8px 18px rgba(110, 31, 43, 0.2); transform: translateY(-1px); }
        .login-btn:focus-visible, .login-footer a:focus-visible { outline: 3px solid rgba(110, 31, 43, 0.25); outline-offset: 3px; }
        .login-btn i { margin-right: 8px; }
        .error-msg {
            background: #fff5f4;
            border: 1px solid #efccca;
            color: #a33c3d;
            padding: 11px 13px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .authorized-line { display: flex; align-items: center; gap: 14px; color: #8d97a6; font-size: 11px; margin: 23px 0 0; text-align: center; }
        .authorized-line::before, .authorized-line::after { content: ''; height: 1px; flex: 1; background: #e1e4e8; }
        .login-footer { text-align: left; margin-top: 25px; padding-top: 0; border-top: 0; }
        .login-footer a { color: var(--burgundy); font-size: 13px; text-decoration: none; font-weight: 600; }
        @media (max-width: 700px) {
            body { padding: 16px; align-items: flex-start; }
            .login-card { display: block; max-width: 480px; min-height: 0; margin: auto; }
            .brand-panel { min-height: 315px; padding: 34px 24px 128px; }
            .brand-mark { width: 62px; height: 62px; margin-bottom: 12px; }
            .brand-panel h1 { font-size: 23px; }
            .brand-panel p { margin-top: 8px; }
            .brand-caption { margin-top: 8px; }
            .brand-photo { height: 34%; }
            .form-panel { padding: 38px 25px 32px; }
            .form-heading h2 { font-size: 31px; }
            .form-heading p { font-size: 14px; }
        }
        @media (max-width: 380px) {
            body { padding: 10px; }
            .form-panel { padding-left: 18px; padding-right: 18px; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-panel">
            <div class="brand-content">
                <img class="brand-mark" src="{{ asset('image/LOGO.png') }}" alt="CASAUL Hotel">
                <h1>CASAUL HOTEL</h1>
                <p>Thoughtful stays, warm hospitality,<br>and seamless hotel operations.</p>
                <span class="brand-caption">HOTEL MANAGEMENT SYSTEM</span>
            </div>
            <img class="brand-photo" src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="CASAUL Hotel room">
        </div>

        <div class="form-panel">
            <div class="form-content">
                <div class="form-heading">
                    <h2>Staff Sign In</h2>
                    <p>Access your CASAUL Hotel workspace</p>
                </div>

                @if($errors->any())
                    <div class="error-msg">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="role">Login As</label>
                <div class="input-wrap">
                    <i class="fas fa-user-tag"></i>
                    <select id="role" name="role" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="housekeeping" {{ old('role') == 'housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                    </select>
                </div>
                @error('role')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="staff@example.com">
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                </div>
            </div>
            <div class="remember-group">
                <input id="remember" type="checkbox" name="remember">
                <label for="remember">Remember me</label>
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
                </form>

                <div class="authorized-line">Authorized Personnel Only</div>
                <div class="login-footer">
                    <a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Website</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

