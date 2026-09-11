<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CASAUL Hotel</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f4f1eb; color: #243447; font-family: Arial, sans-serif; }
        .card { width: min(92%, 440px); padding: 38px 32px; background: #fff; box-shadow: 0 18px 50px rgba(35, 48, 61, .12); }
        h1 { margin: 0 0 12px; color: #1e3a5f; font-size: 28px; }
        p { line-height: 1.5; }
        label { display: block; margin: 22px 0 7px; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #d1d5db; }
        button { margin-top: 18px; border: 0; padding: 13px 20px; background: #1e3a5f; color: #fff; cursor: pointer; }
        a { display: inline-block; margin-top: 20px; color: #1e3a5f; }
        .message { padding: 11px; background: #ecfdf3; color: #23613b; }
        .error { color: #a11; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Forgot Password?</h1>
        <p>Enter your guest account email and we will send you a password reset link.</p>
        @if (session('status')) <p class="message">{{ session('status') }}</p> @endif
        @if ($errors->any()) <p class="error">{{ $errors->first() }}</p> @endif
        <form method="POST" action="{{ route('guest.password.email') }}">
            @csrf
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            <button type="submit">Send Reset Link</button>
        </form>
        <a href="{{ route('home', ['auth' => 'signin']) }}">Return to sign in</a>
    </main>
</body>
</html>
