<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CASAUL Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px 16px; background: radial-gradient(900px 500px at 8% -10%, rgba(143, 14, 22, .10), transparent 60%), #f8f4ef; color: #3f3735; font-family: 'DM Sans', sans-serif; }
        .card { width: min(100%, 480px); padding: 38px 40px 32px; background: #fffdfa; border: 1px solid rgba(143, 14, 22, .08); border-radius: 26px; box-shadow: 0 24px 70px rgba(61, 35, 29, .14); }
        .brand-header { text-align: center; margin-bottom: 26px; }
        .brand-logo { width: 58px; height: 58px; object-fit: contain; margin-bottom: 9px; }
        .brand-name { margin: 0; color: #8f0e16; font-size: 21px; font-weight: 700; letter-spacing: .18em; }
        h1 { margin: 0 0 18px; color: #292324; font-family: 'Libre Baskerville', Georgia, serif; font-size: 25px; font-weight: 400; }
        label { display: block; margin: 18px 0 8px; color: #504846; font-size: 13px; font-weight: 600; }
        input { width: 100%; padding: 13px 14px; border: 1px solid #ded8d5; border-radius: 12px; background: #fff; color: #3f3735; font: inherit; outline: none; }
        input:focus { border-color: #8f0e16; box-shadow: 0 0 0 3px rgba(143, 14, 22, .12); }
        button { width: 100%; margin-top: 22px; border: 0; border-radius: 12px; padding: 14px 20px; background: #8f0e16; color: #fff; cursor: pointer; font: inherit; font-weight: 700; transition: background .2s ease, transform .2s ease; }
        button:hover { background: #721018; transform: translateY(-1px); }
        .error { margin: 0 0 16px; padding: 12px 14px; border-radius: 12px; background: #fff1f0; color: #9b1c1c; font-size: 14px; }
        @media (max-width: 520px) { .card { padding: 30px 22px 26px; border-radius: 22px; } h1 { font-size: 22px; } }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand-header">
            <img src="{{ asset('image/LOGO.png') }}" alt="CASAUL Hotel" class="brand-logo">
            <p class="brand-name">CASAUL</p>
        </div>
        <h1>Set a New Password</h1>
        @if ($errors->any()) <p class="error">{{ $errors->first() }}</p> @endif
        <form method="POST" action="{{ route('guest.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus>
            <label for="password">New password</label>
            <input id="password" type="password" name="password" required>
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
            <button type="submit">Reset Password</button>
        </form>
    </main>
</body>
</html>
