<?php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Profile - CASAUL Hotel</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{font-family:Poppins, sans-serif;background:#f8fafc;padding:24px}</style>
</head>
<body>
    <div style="max-width:760px;margin:40px auto;background:#fff;border-radius:12px;padding:28px;box-shadow:0 8px 30px rgba(0,0,0,0.06)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
            <h2 style="font-weight:700">Your Profile</h2>
            <div style="display:flex;gap:8px;align-items:center">
                <a href="{{ route('accommodations') }}" class="btn"><i class="fas fa-bed"></i> View Accommodation</a>
                <a href="{{ route('records') }}" class="btn"><i class="fas fa-list"></i> View Records</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn" style="background:transparent;border:none;color:#1f2937;cursor:pointer"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>

        @if(session('status'))
            <div style="background:#ecfdf5;border:1px solid #bbf7d0;padding:10px;border-radius:8px;margin-bottom:12px;color:#065f46">
                {{ session('status') }}
            </div>
        @endif

        <div style="display:flex;gap:24px;align-items:center;margin-bottom:20px">
            <div style="width:84px;height:84px;border-radius:9999px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:32px;color:#6b7280">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div style="font-weight:700">{{ auth()->user()->name }}</div>
                <div style="color:#6b7280;font-size:14px">{{ auth()->user()->email }}</div>
                @if(isset(auth()->user()->role))
                    <div style="color:#9ca3af;font-size:13px;margin-top:6px">Role: {{ auth()->user()->role }}</div>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label class="block text-sm font-semibold">Name</label>
                    <input name="name" value="{{ old('name', auth()->user()->name) }}" required style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;margin-top:6px" />
                </div>
                <div>
                    <label class="block text-sm font-semibold">Email</label>
                    <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;margin-top:6px" />
                </div>
            </div>

            <div style="margin-top:12px">
                <label class="block text-sm font-semibold">New Password (leave blank to keep current)</label>
                <input name="password" type="password" autocomplete="new-password" style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;margin-top:6px" />
            </div>

            <div style="margin-top:18px;display:flex;gap:10px">
                <button type="submit" style="background:linear-gradient(135deg,#c9a227,#d4b845);padding:10px 16px;border-radius:8px;font-weight:700;border:none;cursor:pointer">Save Changes</button>
                <a href="{{ route('home') }}" style="padding:10px 16px;border-radius:8px;border:1px solid #e5e7eb;color:#374151;text-decoration:none">Back to Home</a>
            </div>
        </form>
    </div>
</body>
</html>