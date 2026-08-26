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
        * { font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #1e3a5f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
        }
        .login-logo { text-align: center; margin-bottom: 32px; }
        .login-logo img { height: 60px; margin: 0 auto 12px; display: block; }
        .login-logo h1 { font-size: 24px; font-weight: 800; color: #1e3a5f; letter-spacing: 1px; }
        .login-logo p { color: #6b7280; font-size: 14px; margin-top: 4px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; }
        .input-wrap input,
        .input-wrap select {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        .input-wrap input:focus,
        .input-wrap select:focus {
            border-color: #1e3a5f;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.15);
            background: #fff;
        }
        .remember-group { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; }
        .remember-group input[type="checkbox"] { width: 16px; height: 16px; accent-color: #1e3a5f; }
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .login-footer { text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .login-footer a { color: #1e3a5f; font-size: 13px; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('image/LOGO.png') }}" alt="CASAUL Hotel">
            <h1>CASAUL HOTEL</h1>
            <p>Staff Access</p>
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

        <div class="login-footer">
            <a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Website</a>
        </div>
    </div>
</body>
</html>

