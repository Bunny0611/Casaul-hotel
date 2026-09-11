<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Email - CASAUL Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px 16px; background: radial-gradient(900px 500px at 8% -10%, rgba(143, 14, 22, .10), transparent 60%), #f8f4ef; color: #3f3735; font-family: 'DM Sans', sans-serif; }
        .card { width: min(100%, 480px); padding: 38px 40px 32px; text-align: center; background: #fffdfa; border: 1px solid rgba(143, 14, 22, .08); border-radius: 26px; box-shadow: 0 24px 70px rgba(61, 35, 29, .14); }
        .brand-logo { width: 58px; height: 58px; object-fit: contain; margin-bottom: 9px; }
        .brand-name { margin: 0 0 26px; color: #8f0e16; font-size: 21px; font-weight: 700; letter-spacing: .18em; }
        h1 { margin: 0 0 10px; color: #292324; font-family: 'Libre Baskerville', Georgia, serif; font-size: 25px; font-weight: 400; }
        p { line-height: 1.65; margin: 10px 0; color: #817775; }
        .email { color: #8f0e16; font-weight: 700; overflow-wrap: anywhere; }
        button { width: 100%; margin-top: 20px; border: 0; border-radius: 12px; padding: 14px 22px; background: #8f0e16; color: #fff; cursor: pointer; font: inherit; font-weight: 700; transition: background .2s ease, transform .2s ease; }
        button:hover { background: #721018; transform: translateY(-1px); }
        a { display: inline-block; margin-top: 22px; color: #8f0e16; font-size: 14px; font-weight: 700; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .status { margin: 20px 0; padding: 12px 14px; border-radius: 12px; background: #edf8f0; color: #23613b; font-size: 14px; }
        @media (max-width: 520px) { .card { padding: 30px 22px 26px; border-radius: 22px; } h1 { font-size: 22px; } }
    </style>
</head>
<body>
    <main class="card">
        <img src="{{ asset('image/LOGO.png') }}" alt="CASAUL Hotel" class="brand-logo">
        <p class="brand-name">CASAUL</p>
        <h1>Check Your Email</h1>
        <p>Thanks{{ $name ? ', '.$name : '' }}. We sent a verification link to:</p>
        <p class="email">{{ $email }}</p>
        <p>Click the link in that email to verify your account. You will then be sent to the guest login page.</p>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('guest.verification.send') }}">
            @csrf
            <button type="submit">Resend Verification Email</button>
        </form>
        <a href="{{ route('home', ['auth' => 'signin']) }}">Return to guest login</a>
    </main>
</body>
</html>