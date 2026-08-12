<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CASAUL Hotel - Guest Access</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #1e3a5f 100%);
            color: #1f2937;
        }
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 32px 20px;
            overflow-x: hidden;
            overflow-y: auto;
            align-items: flex-start;
        }
        .page-wrapper {
            width: 100%;
            max-width: 500px;
            display: block;
            margin: 0 auto;
        }
        .login-card {
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 28px;
            box-shadow: 0 28px 90px rgba(15, 23, 42, 0.18);
            padding: 36px 34px;
            overflow: visible;
            max-height: none;
            min-height: auto;
        }
        .login-card-inner {
            display: grid;
            gap: 28px;
        }
        .login-logo {
            text-align: center;
        }
        .login-logo img {
            height: 56px;
            margin-bottom: 14px;
        }
        .login-logo h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 0.08em;
            color: #1e3a5f;
        }
        .login-logo p {
            margin: 8px auto 0;
            color: #475569;
            font-size: 14px;
        }
        .auth-tabs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .tab-button {
            padding: 14px 18px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .tab-button.active {
            background: #1e3a5f;
            color: white;
            border-color: #1e3a5f;
        }
        .section-title {
            margin: 0 0 10px;
            font-size: 20px;
            color: #0f172a;
        }
        .section-subtitle {
            margin: 0 0 22px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }
        .input-wrap input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .input-wrap input:focus {
            border-color: #1e3a5f;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.08);
            outline: none;
            background: #ffffff;
        }
        .action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .remember-group {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #475569;
            font-size: 14px;
        }
        .remember-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #1e3a5f;
        }
        .login-btn {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 15px;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            cursor: pointer;
            transition: transform 0.2s ease, filter 0.2s ease;
        }
        .login-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }
        .login-footer {
            text-align: center;
            margin-top: 12px;
        }
        .login-footer a {
            color: #1e3a5f;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hidden { display: none !important; }
        @media (max-width: 520px) {
            body { padding: 18px 10px; }
            .login-card { padding: 24px 18px; }
            .auth-tabs { grid-template-columns: 1fr; }
            .login-card {
                border-radius: 24px;
            }
            .section-title {
                font-size: 18px;
            }
            .input-wrap input {
                padding: 12px 12px 12px 42px;
            }
            .login-btn {
                padding: 13px 14px;
            }
        }
        @media (max-height: 720px) {
            .page-wrapper { align-items: flex-start; }
            .login-card { max-height: none; }
        }
    </style>
</head>
<body>
    @php
        $showSignup = old('first_name') || old('last_name') || old('middle_initial') || old('contact_no') || old('password_confirmation');
    @endphp
    <div class="page-wrapper">
        <div class="login-card">
            <div class="login-card-inner">
                <div class="login-logo">
                    <img src="{{ asset('image/LOGO.png') }}" alt="CASAUL Hotel logo">
                    <h1>CASAUL HOTEL</h1>
                    <p>Guest sign in or create a guest account below.</p>
                </div>

                <div class="auth-tabs" role="tablist" aria-label="Guest auth tabs">
                    <button type="button" id="tab-signin" class="tab-button{{ $showSignup ? '' : ' active' }}" aria-selected="{{ $showSignup ? 'false' : 'true' }}">Sign In</button>
                    <button type="button" id="tab-signup" class="tab-button{{ $showSignup ? ' active' : '' }}" aria-selected="{{ $showSignup ? 'true' : 'false' }}">Sign Up</button>
                </div>

                @if($errors->any())
                    <div class="error-msg" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>There was a problem with your submission.</strong>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    </div>
                @endif

                <section id="signin-view" class="{{ $showSignup ? 'hidden' : '' }}">
                    <h2 class="section-title">Guest Sign In</h2>
                    <p class="section-subtitle">Use your email and password to access guest services.</p>
                    <form method="POST" action="{{ route('guest.login.submit') }}">
                        @csrf
                        <div class="form-group">
                            <label for="signin-email">Email Address</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope"></i>
                                <input id="signin-email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="guest@example.com">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signin-password">Password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock"></i>
                                <input id="signin-password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                            </div>
                        </div>
                        <div class="action-row">
                            <label class="remember-group">
                                <input type="checkbox" name="remember">
                                Remember me
                            </label>
                            <a href="{{ route('home') }}" class="login-footer">Back to website</a>
                        </div>
                        <button type="submit" class="login-btn">Sign In</button>
                    </form>
                </section>

                <section id="signup-view" class="{{ $showSignup ? '' : 'hidden' }}" aria-hidden="{{ $showSignup ? 'false' : 'true' }}">
                    <h2 class="section-title">Create Guest Account</h2>
                    <p class="section-subtitle">Register once and access reservations, messages, and more.</p>
                    <form method="POST" action="{{ route('guest.register.submit') }}">
                        @csrf
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <div class="input-wrap">
                                <i class="fas fa-user"></i>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="First name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <div class="input-wrap">
                                <i class="fas fa-user"></i>
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Last name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="middle_initial">Middle Initial</label>
                            <div class="input-wrap">
                                <i class="fas fa-id-card"></i>
                                <input id="middle_initial" type="text" name="middle_initial" value="{{ old('middle_initial') }}" maxlength="3" required placeholder="M.I.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-email">Email Address</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope"></i>
                                <input id="signup-email" type="email" name="email" value="{{ old('email') }}" required placeholder="guest@example.com">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact_no">Contact Number</label>
                            <div class="input-wrap">
                                <i class="fas fa-phone"></i>
                                <input id="contact_no" type="text" name="contact_no" value="{{ old('contact_no') }}" required placeholder="09xxxxxxxxx">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-password">Password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock"></i>
                                <input id="signup-password" type="password" name="password" required placeholder="Choose a password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock"></i>
                                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repeat password">
                            </div>
                        </div>
                        <button type="submit" class="login-btn">Create Account</button>
                    </form>
                </section>

                <div class="login-footer">
                    <a href="{{ route('home') }}">Back to website</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const signinTab = document.getElementById('tab-signin');
        const signupTab = document.getElementById('tab-signup');
        const signinView = document.getElementById('signin-view');
        const signupView = document.getElementById('signup-view');

        function activateTab(isSignup) {
            if (isSignup) {
                signupTab.classList.add('active');
                signinTab.classList.remove('active');
                signupView.classList.remove('hidden');
                signinView.classList.add('hidden');
                signupView.setAttribute('aria-hidden', 'false');
                signinView.setAttribute('aria-hidden', 'true');
            } else {
                signinTab.classList.add('active');
                signupTab.classList.remove('active');
                signinView.classList.remove('hidden');
                signupView.classList.add('hidden');
                signinView.setAttribute('aria-hidden', 'false');
                signupView.setAttribute('aria-hidden', 'true');
            }
        }

        signinTab?.addEventListener('click', () => activateTab(false));
        signupTab?.addEventListener('click', () => activateTab(true));
    </script>
</body>
</html>
