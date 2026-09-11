<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Email - CASAUL Hotel</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f4f1eb; color: #243447; font-family: Georgia, serif; }
        .card { width: min(92%, 460px); padding: 42px 34px; text-align: center; background: #fff; box-shadow: 0 18px 50px rgba(35, 48, 61, .12); }
        h1 { margin: 0 0 14px; color: #1e3a5f; font-size: 30px; }
        p { line-height: 1.6; margin: 10px 0; }
        .email { color: #1e3a5f; font-weight: 700; overflow-wrap: anywhere; }
        button { margin-top: 20px; border: 0; padding: 13px 22px; background: #1e3a5f; color: #fff; cursor: pointer; font: inherit; }
        a { display: inline-block; margin-top: 20px; color: #1e3a5f; }
        .status { color: #23613b; margin-bottom: 18px; }
    </style>
</head>
<body>
    <main class="card">
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