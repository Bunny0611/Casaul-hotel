<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CASAUL Hotel</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f4f1eb; color: #243447; font-family: Arial, sans-serif; }
        .card { width: min(92%, 440px); padding: 38px 32px; background: #fff; box-shadow: 0 18px 50px rgba(35, 48, 61, .12); }
        h1 { margin: 0 0 12px; color: #1e3a5f; font-size: 28px; }
        label { display: block; margin: 18px 0 7px; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #d1d5db; }
        button { margin-top: 22px; border: 0; padding: 13px 20px; background: #1e3a5f; color: #fff; cursor: pointer; }
        .error { color: #a11; }
    </style>
</head>
<body>
    <main class="card">
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
