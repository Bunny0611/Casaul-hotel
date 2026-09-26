<x-mail::message>
<div style="margin: 0 0 22px; overflow: hidden; border-radius: 8px;">
    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="A room at Casaul Hotel" width="570" style="display: block; width: 100%; max-width: 570px; height: auto; border: 0;">
</div>

@if ($actionText === 'Verify Email Address')
    <p style="margin: 0 0 8px; color: #cc0925; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Account Verification</p>
    <h1 style="margin: 0 0 14px; color: #1f3551; font-size: 28px; line-height: 1.25;">Verify Your Email Address</h1>
    <p style="margin: 0 0 12px; color: #263548;">{{ $greeting ?: 'Hello!' }}</p>
    <p style="margin: 0 0 20px; color: #5b6572; line-height: 1.65;">Welcome to Casaul Hotel! Please verify your email address to activate your account and continue using your account.</p>
@else
    <p style="margin: 0 0 8px; color: #cc0925; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Account Security</p>
    <h1 style="margin: 0 0 14px; color: #1f3551; font-size: 28px; line-height: 1.25;">Reset Your Password</h1>
    <p style="margin: 0 0 12px; color: #263548;">{{ $greeting ?: 'Hello!' }}</p>
    <p style="margin: 0 0 20px; color: #5b6572; line-height: 1.65;">We received a request to reset the password for your Casaul Hotel account.</p>
@endif

<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto 20px;">
    <tr><td align="center" bgcolor="#cc0925" style="border-radius: 5px; background-color: #cc0925;">
        <a href="{{ $actionUrl }}" style="display: inline-block; padding: 14px 25px; border: 1px solid #cc0925; border-radius: 5px; color: #ffffff; font-size: 14px; font-weight: 700; line-height: 1.2; text-decoration: none;">{{ $actionText }}</a>
    </td></tr>
</table>

<p style="margin: 0 0 6px; color: #737d89; font-size: 12px; line-height: 1.5;">If the button does not work, copy and paste this link into your browser:</p>
<p style="margin: 0 0 20px; color: #cc0925; font-size: 12px; line-height: 1.5; word-break: break-all;">
    <a href="{{ $actionUrl }}" style="color: #cc0925; text-decoration: underline;">{{ $displayableActionUrl }}</a>
</p>

@if ($actionText === 'Verify Email Address')
    <p style="margin: 0 0 12px; color: #737d89; font-size: 13px; line-height: 1.6;">This verification link will expire in {{ config('auth.verification.expire', 60) }} minutes.</p>
    <p style="margin: 0 0 18px; color: #737d89; font-size: 13px; line-height: 1.6;">If you did not create a Casaul Hotel account, you can safely ignore this email.</p>
@else
    @foreach ($outroLines as $line)
        <p style="margin: 0 0 10px; color: #737d89; font-size: 13px; line-height: 1.6;">{{ $line }}</p>
    @endforeach
@endif

<p style="margin: 0; color: #5b6572; line-height: 1.6;">Regards,<br><strong style="color: #1f3551;">Casaul Hotel Team</strong></p>
</x-mail::message>