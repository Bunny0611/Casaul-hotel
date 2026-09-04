@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $cta = $cta ?? null;
    $backgroundImage = $backgroundImage ?? null;
    $backgroundVideo = $backgroundVideo ?? null;
    $ctaRequiresGuestAuth = $cta['requiresGuestAuth'] ?? false;
    $ctaNeedsAuth = $ctaRequiresGuestAuth && !auth('guest')->check();
@endphp

<section class="hero">
    <div class="media">
        @if(!empty($backgroundVideo))
            <video class="bg-video" autoplay muted loop playsinline>
                <source src="{{ asset($backgroundVideo) }}" type="video/mp4">
            </video>
        @endif

        @if(!empty($backgroundImage))
            <img class="bg-image" src="{{ asset($backgroundImage) }}" alt="">
        @endif
    </div>

    <div class="overlay">
        <h1>{{ $title }}</h1>

        @if(!empty($subtitle))
            <p>{{ $subtitle }}</p>
        @endif

        @if(!empty($cta))
            <a href="{{ $ctaNeedsAuth ? '#guest-auth-modal' : $cta['href'] }}" class="btn{{ $ctaNeedsAuth ? ' js-auth-trigger' : '' }}"{{ $ctaNeedsAuth ? ' data-auth-trigger' : '' }}>{{ $cta['label'] }}</a>
        @endif
    </div>
</section>


