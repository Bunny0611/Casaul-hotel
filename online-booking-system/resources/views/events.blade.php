@extends('app')

@section('content')

<main class="events-page">
    <section class="events-hero" aria-labelledby="events-hero-title">
        <div class="events-hero-copy">
            <p class="events-eyebrow">Celebrate Something Special</p>
            <h1 id="events-hero-title">Weddings &amp; Birthdays</h1>
            <p class="events-hero-subtitle">Beautiful moments, thoughtfully celebrated at CASAUL HOTEL.</p>
            <p class="events-hero-description">From intimate birthday celebrations to unforgettable wedding receptions, create your special day in a setting designed around you.</p>
            <div class="events-hero-actions">
                <a href="{{ route('reservation') }}" class="events-button events-button--primary">Plan a Wedding <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('reservation') }}" class="events-button events-button--outline">Plan a Birthday <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
        <div class="events-hero-image">
            <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&amp;fit=crop&amp;w=1800&amp;q=85" alt="Romantic wedding celebration in an elegant outdoor setting">
        </div>
    </section>

    <section class="events-celebrations" aria-labelledby="celebrations-title">
        <div class="events-section-heading">
            <p class="events-eyebrow">Choose Your Celebration</p>
            <h2 id="celebrations-title">Celebrate Your Way</h2>
        </div>
        <div class="events-card-grid">
            <article class="events-detail-card" id="wedding-details">
                <div class="events-detail-image">
                    <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&amp;fit=crop&amp;w=1100&amp;q=85" alt="Elegant wedding reception venue dressed for an intimate celebration" loading="lazy">
                    <span class="events-detail-icon" aria-hidden="true"><i class="fas fa-ring"></i></span>
                </div>
                <div class="events-detail-copy">
                    <h3>Weddings</h3>
                    <p>Celebrate your love in an elegant setting surrounded by the people who matter most.</p>
                    <ul>
                        <li>Wedding Receptions</li>
                        <li>Intimate Celebrations</li>
                        <li>Elegant Dining</li>
                        <li>Customizable Setup</li>
                    </ul>
                    <a href="#wedding-promotion" class="events-text-link">View Wedding Details <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>
            <article class="events-detail-card" id="birthday-details">
                <div class="events-detail-image">
                    <img src="https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&amp;fit=crop&amp;w=1100&amp;q=85" alt="Colorful birthday celebration with balloons and festive decorations" loading="lazy">
                    <span class="events-detail-icon" aria-hidden="true"><i class="fas fa-cake-candles"></i></span>
                </div>
                <div class="events-detail-copy">
                    <h3>Birthdays</h3>
                    <p>Make every birthday memorable with a stylish celebration, delicious dining, and a space made for your guests.</p>
                    <ul>
                        <li>Birthday Celebrations</li>
                        <li>Private Gatherings</li>
                        <li>Dining &amp; Catering</li>
                        <li>Customizable Setup</li>
                    </ul>
                    <a href="#birthday-promotion" class="events-text-link">View Birthday Details <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>
        </div>
    </section>

    <section class="events-experience" aria-labelledby="experience-title">
        <div class="events-section-heading">
            <p class="events-eyebrow">The Casaul Experience</p>
            <h2 id="experience-title">Your Celebration, Our Hospitality</h2>
        </div>
        <div class="events-experience-grid">
            <article>
                <svg class="events-experience-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M5 28V11l8-4v21M13 13h8v15M21 17h6v11M3 28h26M8 14h2M8 18h2M8 22h2M16 17h2M16 21h2M23 20h2M23 24h2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3>Elegant Spaces</h3>
                <p>Beautifully designed spaces suitable for intimate and memorable celebrations.</p>
            </article>
            <article>
                <svg class="events-experience-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M8 3v8M5 3v5a3 3 0 0 0 6 0V3M8 11v18M22 3v26M22 3c5 3 7 8 0 12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3>Exceptional Dining</h3>
                <p>Thoughtfully prepared food and beverage options for your guests.</p>
            </article>
            <article>
                <svg class="events-experience-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true"><rect x="4" y="7" width="24" height="21" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M10 3v8M22 3v8M4 13h24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                <h3>Personalized Setup</h3>
                <p>Flexible arrangements designed around your celebration.</p>
            </article>
            <article>
                <svg class="events-experience-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="11" cy="10" r="4" stroke="currentColor" stroke-width="1.7"/><circle cx="22" cy="11" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 27v-2a8 8 0 0 1 16 0v2M19 19a7 7 0 0 1 10 6v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                <h3>Warm Hospitality</h3>
                <p>Our team is here to make your event comfortable and memorable.</p>
            </article>
        </div>
    </section>

    <section class="events-promotion events-promotion--wedding" id="wedding-promotion" aria-labelledby="wedding-promotion-title">
        <img src="https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&amp;fit=crop&amp;w=1200&amp;q=85" alt="Candlelit wedding reception table with elegant floral details" loading="lazy">
        <div class="events-promotion-copy">
            <p class="events-eyebrow">For Your Special Day</p>
            <h2 id="wedding-promotion-title">Begin Your Forever at CASAUL</h2>
            <p>From elegant wedding receptions to intimate ceremonies, we provide a beautiful setting for your most meaningful moments.</p>
            <a href="{{ route('reservation') }}" class="events-button events-button--primary">Start Planning Your Wedding <span aria-hidden="true">&rarr;</span></a>
            <svg class="events-promotion-ornament events-promotion-ornament--right" viewBox="0 0 80 180" fill="none" aria-hidden="true"><path d="M67 177C50 146 56 112 42 82S35 38 47 8M46 46C31 43 22 33 19 19C35 22 44 31 46 46ZM48 79C63 67 69 53 65 38C52 46 47 60 48 79ZM54 109C39 104 29 93 27 77C43 83 53 94 54 109ZM60 138C72 128 77 116 74 103C62 111 58 124 60 138Z" stroke="currentColor" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
    </section>

    <section class="events-promotion events-promotion--birthday" id="birthday-promotion" aria-labelledby="birthday-promotion-title">
        <div class="events-promotion-copy">
            <p class="events-eyebrow">Make It Memorable</p>
            <h2 id="birthday-promotion-title">Celebrate Another Beautiful Year</h2>
            <p>Gather your favorite people, enjoy great food, and celebrate another year in a comfortable and elegant CASAUL HOTEL setting.</p>
            <a href="{{ route('reservation') }}" class="events-button events-button--primary">Start Planning Your Birthday <span aria-hidden="true">&rarr;</span></a>
            <svg class="events-promotion-ornament events-promotion-ornament--left" viewBox="0 0 80 180" fill="none" aria-hidden="true"><path d="M13 177C30 146 24 112 38 82S45 38 33 8M34 46C49 43 58 33 61 19C45 22 36 31 34 46ZM32 79C17 67 11 53 15 38C28 46 33 60 32 79ZM26 109C41 104 51 93 53 77C37 83 27 94 26 109ZM20 138C8 128 3 116 6 103C18 111 22 124 20 138Z" stroke="currentColor" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <img src="https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&amp;fit=crop&amp;w=1200&amp;q=85" alt="Birthday cake and balloons arranged for a festive celebration" loading="lazy">
    </section>

    <section class="events-final-cta" aria-labelledby="events-final-title">
        <svg class="events-ornament events-ornament--left" viewBox="0 0 150 190" fill="none" aria-hidden="true"><path d="M20 180C42 139 42 98 27 55M27 55C17 37 16 22 24 10M27 55C45 43 55 28 57 12M36 91C18 84 8 73 5 59M39 111C58 104 71 92 78 75M43 135C26 130 16 119 12 103M48 154C69 148 82 135 88 118" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M23 29C12 30 6 25 5 17C14 14 21 18 23 29ZM48 31C47 20 52 13 61 11C64 20 59 28 48 31ZM17 76C8 69 7 61 12 53C21 59 23 67 17 76ZM64 94C64 83 70 76 79 75C81 85 76 92 64 94ZM22 120C12 114 10 106 14 97C24 103 27 111 22 120ZM73 139C73 128 79 121 89 119C91 129 85 137 73 139Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
        <div class="events-final-copy">
            <p class="events-eyebrow">Ready to Celebrate?</p>
            <h2 id="events-final-title">Let&rsquo;s Make It Special.</h2>
            <p>Choose your celebration and begin your reservation with CASAUL HOTEL.</p>
            <div class="events-final-actions">
                <a href="{{ route('reservation') }}" class="events-button events-button--final-outline">Wedding Reservation <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('reservation') }}" class="events-button events-button--final-gold">Birthday Reservation <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
        <svg class="events-ornament events-ornament--right" viewBox="0 0 150 190" fill="none" aria-hidden="true"><path d="M130 180C108 139 108 98 123 55M123 55C133 37 134 22 126 10M123 55C105 43 95 28 93 12M114 91C132 84 142 73 145 59M111 111C92 104 79 92 72 75M107 135C124 130 134 119 138 103M102 154C81 148 68 135 62 118" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M127 29C138 30 144 25 145 17C136 14 129 18 127 29ZM102 31C103 20 98 13 89 11C86 20 91 28 102 31ZM133 76C142 69 143 61 138 53C129 59 127 67 133 76ZM86 94C86 83 80 76 71 75C69 85 74 92 86 94ZM128 120C138 114 140 106 136 97C126 103 123 111 128 120ZM77 139C77 128 71 121 61 119C59 129 65 137 77 139Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
    </section>
</main>

<style>
    .events-page {
        --events-burgundy: #70282d;
        --events-burgundy-deep: #552025;
        --events-gold: #b38a45;
        --events-cream: #fbf7f0;
        --events-blush: #f8efeb;
        --events-ink: #332725;
        --events-muted: #746764;
        padding: 0 24px 76px;
        color: var(--events-ink);
        font-family: 'DM Sans', sans-serif;
    }

    .events-page h1,
    .events-page h2,
    .events-page h3 {
        color: var(--events-burgundy-deep);
        font-family: 'Libre Baskerville', Georgia, serif;
        font-weight: 400;
        letter-spacing: 0;
    }

    .events-page p { color: var(--events-muted); }

    .events-eyebrow {
        margin: 0 0 14px;
        color: var(--events-gold) !important;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.17em;
        line-height: 1.4;
        text-transform: uppercase;
    }

    .events-hero,
    .events-celebrations,
    .events-experience,
    .events-promotion,
    .events-final-cta {
        width: min(1200px, 100%);
        margin-right: auto;
        margin-left: auto;
    }

    .events-hero {
        position: relative;
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 100vw;
        max-width: none;
        margin-left: calc(50% - 50vw);
        min-height: 490px;
        align-items: center;
        overflow: hidden;
        border-radius: 12px;
        background: var(--events-cream);
        box-shadow: 0 18px 54px rgba(78, 35, 31, 0.1);
    }

    .events-hero-copy {
        position: relative;
        z-index: 2;
        grid-column: 1 / 2;
        padding: 112px 0 56px 62px;
    }

    .events-hero h1 {
        max-width: 520px;
        margin: 0;
        font-size: clamp(2.8rem, 4.8vw, 4.6rem);
        line-height: 1.12;
    }

    .events-hero-subtitle {
        max-width: 440px;
        margin: 20px 0 12px;
        color: var(--events-burgundy) !important;
        font-family: 'Libre Baskerville', Georgia, serif;
        font-size: 1.05rem;
        font-style: italic;
        line-height: 1.7;
    }

    .events-hero-description {
        max-width: 430px;
        margin: 0;
        font-size: 0.98rem;
        line-height: 1.8;
    }

    .events-hero-image {
        position: absolute;
        inset: 0 0 0 35%;
        z-index: 1;
    }

    .events-hero-image::after {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, var(--events-cream) 0%, rgba(251, 247, 240, 0.95) 10%, rgba(251, 247, 240, 0.48) 38%, rgba(251, 247, 240, 0) 72%);
        content: '';
    }

    .events-hero-image img,
    .events-detail-image > img,
    .events-promotion > img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .events-hero-image img { object-position: center 48%; }

    .events-hero-actions,
    .events-final-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
    }

    .events-button {
        display: inline-flex;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 12px 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.86rem;
        font-weight: 700;
        line-height: 1.35;
        text-align: center;
        text-decoration: none;
        transition: background-color 160ms ease, color 160ms ease, transform 160ms ease;
    }

    .events-button:hover { transform: translateY(-2px); }

    .events-button--primary {
        border-color: var(--events-burgundy);
        background: var(--events-burgundy);
        color: #fff !important;
    }

    .events-button--primary:hover { background: var(--events-burgundy-deep); }

    .events-button--outline {
        border-color: var(--events-gold);
        background: rgba(255, 255, 255, 0.86);
        color: var(--events-burgundy) !important;
    }

    .events-button--outline:hover { background: #fff; }

    .events-celebrations { padding-top: 82px; }

    .events-section-heading {
        margin: 0 auto 34px;
        text-align: center;
    }

    .events-section-heading .events-eyebrow { margin-bottom: 10px; }

    .events-section-heading h2 {
        margin: 0;
        font-size: clamp(2rem, 3.5vw, 2.8rem);
        line-height: 1.3;
    }

    .events-card-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 26px;
    }

    .events-detail-card {
        overflow: hidden;
        border: 1px solid rgba(179, 138, 69, 0.22);
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 12px 38px rgba(78, 35, 31, 0.08);
        scroll-margin-top: 120px;
    }

    .events-detail-image {
        position: relative;
        aspect-ratio: 3 / 1;
        min-height: 145px;
    }

    .events-detail-icon {
        position: absolute;
        bottom: -25px;
        left: 20px;
        z-index: 1;
        display: grid;
        width: 54px;
        height: 54px;
        place-items: center;
        border: 3px solid #fff;
        border-radius: 50%;
        background: #d9656c;
        color: #fff;
        font-size: 1.1rem;
    }

    .events-detail-copy { padding: 20px 24px 15px; }

    .events-detail-copy h3 {
        margin: 0 0 4px;
        font-size: 1.35rem;
        line-height: 1.2;
    }

    .events-detail-copy > p {
        min-height: 36px;
        margin: 0 0 9px;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    .events-detail-copy ul {
        display: grid;
        grid-template-columns: 1fr;
        gap: 3px;
        margin: 0 0 11px;
        padding: 0;
        list-style: none;
    }

    .events-detail-copy li {
        position: relative;
        padding-left: 22px;
        color: var(--events-muted);
        font-size: 0.74rem;
        line-height: 1.3;
    }

    .events-detail-copy li::before {
        position: absolute;
        top: 0.16em;
        left: 0;
        display: grid;
        width: 13px;
        height: 13px;
        place-items: center;
        border-radius: 50%;
        background: #d58a18;
        color: #fff;
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-size: 7px;
        font-weight: 900;
    }

    .events-text-link {
        display: flex;
        width: 100%;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 10px 16px;
        border-radius: 7px;
        background: var(--events-burgundy);
        color: #fff !important;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        transition: background-color 160ms ease, transform 160ms ease;
    }

    .events-text-link:hover {
        background: var(--events-burgundy-deep);
        color: #fff !important;
        transform: translateY(-1px);
    }

    .events-experience { padding-top: 84px; }

    .events-experience .events-section-heading { margin-bottom: 22px; }

    .events-experience-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        width: 100%;
        margin: 0 auto;
        text-align: left;
    }

    .events-experience-grid article {
        position: relative;
        padding: 0 24px;
    }

    .events-experience-grid article:not(:last-child)::after {
        position: absolute;
        top: 5px;
        right: 0;
        bottom: 2px;
        width: 1px;
        background: rgba(179, 138, 69, 0.28);
        content: '';
    }

    .events-experience-icon {
        display: block;
        width: 28px;
        height: 28px;
        margin-bottom: 8px;
        color: var(--events-gold);
    }

    .events-experience-grid h3 {
        margin: 0 0 6px;
        font-size: 0.95rem;
    }

    .events-experience-grid p {
        max-width: 180px;
        margin: 0;
        font-size: 0.72rem;
        line-height: 1.45;
    }

    .events-promotion {
        display: grid;
        grid-template-columns: 1fr 1fr;
        height: 190px;
        min-height: 190px;
        margin-top: 28px;
        overflow: hidden;
        border: 1px solid rgba(179, 138, 69, 0.16);
        border-radius: 10px;
        background: var(--events-cream);
        box-shadow: 0 14px 38px rgba(78, 35, 31, 0.07);
        scroll-margin-top: 120px;
    }

    .events-promotion--birthday {
        margin-top: 16px;
        background: var(--events-blush);
    }

    .events-promotion > img { min-height: 0; }

    .events-promotion-copy {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        padding: 14px 32px;
    }

    .events-promotion--wedding .events-promotion-copy { padding-right: 72px; }
    .events-promotion--birthday .events-promotion-copy { padding-left: 72px; }

    .events-promotion-copy > .events-eyebrow {
        margin-bottom: 4px;
        font-size: 0.58rem;
    }

    .events-promotion-copy h2 {
        max-width: 330px;
        margin: 0 0 7px;
        font-size: 1.55rem;
        line-height: 1.08;
    }

    .events-promotion-copy > p:not(.events-eyebrow) {
        max-width: 400px;
        margin: 0;
        font-size: 0.72rem;
        line-height: 1.45;
    }

    .events-promotion-copy .events-button {
        min-height: 34px;
        margin-top: 10px;
        padding: 7px 14px;
        font-size: 0.72rem;
    }

    .events-promotion-ornament {
        position: absolute;
        top: 50%;
        width: 50px;
        height: 130px;
        color: rgba(179, 138, 69, 0.3);
        pointer-events: none;
        transform: translateY(-50%);
    }

    .events-promotion-ornament--right { right: 12px; }
    .events-promotion-ornament--left { left: 12px; }

    .events-final-cta {
        position: relative;
        display: flex;
        min-height: 108px;
        align-items: center;
        justify-content: center;
        margin-top: 18px;
        padding: 16px 76px;
        overflow: hidden;
        border: 1px solid rgba(224, 194, 131, 0.58);
        border-radius: 10px;
        background: var(--events-burgundy-deep);
        text-align: left;
    }

    .events-final-copy {
        position: relative;
        z-index: 1;
        display: grid;
        width: 100%;
        grid-template-columns: minmax(0, 1fr) auto;
        grid-template-rows: auto auto auto;
        align-items: center;
        column-gap: 22px;
        padding: 0;
    }

    .events-final-copy .events-eyebrow {
        grid-column: 1;
        grid-row: 1;
        margin-bottom: 2px;
        font-size: 0.56rem;
    }

    .events-final-copy h2 {
        grid-column: 1;
        grid-row: 2;
        margin: 0;
        color: #fff8ed;
        font-size: 1.4rem;
        line-height: 1.2;
    }

    .events-final-copy > p:not(.events-eyebrow) {
        grid-column: 1;
        grid-row: 3;
        margin: 3px 0 0;
        color: rgba(255, 248, 237, 0.82);
        font-size: 0.72rem;
        line-height: 1.4;
    }

    .events-final-actions {
        grid-column: 2;
        grid-row: 1 / 4;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 0;
    }

    .events-final-actions .events-button {
        min-height: 36px;
        padding: 8px 12px;
        font-size: 0.7rem;
        white-space: nowrap;
    }

    .events-button--final-outline {
        border-color: rgba(235, 208, 157, 0.85);
        background: transparent;
        color: #fff8ed !important;
    }

    .events-button--final-outline:hover { background: rgba(255, 255, 255, 0.08); }

    .events-button--final-gold {
        border-color: #e6c98d;
        background: #e6c98d;
        color: var(--events-burgundy-deep) !important;
    }

    .events-button--final-gold:hover { background: #f1ddb0; }

    .events-ornament {
        position: absolute;
        top: 50%;
        z-index: 0;
        width: 90px;
        height: 138px;
        color: rgba(230, 201, 141, 0.54);
        pointer-events: none;
        transform: translateY(-50%);
    }

    .events-ornament--left { left: 10px; }
    .events-ornament--right { right: 10px; }

    @media (max-width: 900px) {
        .events-hero { min-height: 450px; }
        .events-hero-copy { padding-left: 38px; }
        .events-hero-image { left: 30%; }
        .events-card-grid { gap: 18px; }
        .events-detail-copy { padding-right: 24px; padding-left: 24px; }
        .events-experience-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px 0; }
        .events-experience-grid article:nth-child(2)::after { display: none; }
        .events-promotion { height: 220px; min-height: 220px; }
        .events-promotion > img { min-height: 0; }
        .events-final-cta { padding-right: 56px; padding-left: 56px; }
        .events-final-copy { column-gap: 14px; }
        .events-final-copy h2 { font-size: 1.25rem; }
        .events-final-copy > p:not(.events-eyebrow) { font-size: 0.66rem; }
        .events-final-actions { gap: 7px; }
        .events-final-actions .events-button { padding-right: 9px; padding-left: 9px; font-size: 0.62rem; }
        .events-ornament { width: 72px; height: 116px; }
        .events-ornament--left { left: 6px; }
        .events-ornament--right { right: 6px; }
    }

    @media (max-width: 680px) {
        .events-page { padding: 0 16px 52px; }
        .events-hero { display: block; min-height: 0; }
        .events-hero-copy { padding: 215px 26px 30px; }
        .events-hero h1 { font-size: clamp(2.35rem, 10vw, 3.4rem); }
        .events-hero-subtitle { margin-top: 14px; font-size: 0.95rem; }
        .events-hero-description { font-size: 0.9rem; }
        .events-hero-image { inset: 0 0 auto; height: 250px; }
        .events-hero-image::after { background: linear-gradient(180deg, rgba(251, 247, 240, 0) 45%, var(--events-cream) 100%); }
        .events-hero-actions { display: grid; grid-template-columns: 1fr; margin-top: 20px; }
        .events-button { width: 100%; }
        .events-celebrations,
        .events-experience { padding-top: 60px; }
        .events-section-heading { margin-bottom: 26px; }
        .events-section-heading h2 { font-size: 1.85rem; }
        .events-card-grid { grid-template-columns: 1fr; gap: 18px; }
        .events-detail-image { aspect-ratio: 1.8 / 1; min-height: 170px; }
        .events-detail-copy { padding: 23px 22px 25px; }
        .events-detail-copy > p { min-height: 0; }
        .events-experience-grid { gap: 26px 0; }
        .events-experience-grid article { padding: 0 12px; }
        .events-experience-grid h3 { font-size: 1rem; }
        .events-experience-grid p { font-size: 0.82rem; }
        .events-promotion,
        .events-promotion--birthday { grid-template-columns: 1fr; height: auto; min-height: 0; margin-top: 54px; }
        .events-promotion--birthday .events-promotion-copy { order: 2; }
        .events-promotion--birthday > img { order: 1; }
        .events-promotion > img { min-height: 0; height: 230px; }
        .events-promotion-copy { padding: 28px 23px 30px; }
        .events-promotion--wedding .events-promotion-copy,
        .events-promotion--birthday .events-promotion-copy { padding: 24px 23px; }
        .events-promotion-copy h2 { max-width: 100%; font-size: 1.45rem; }
        .events-promotion-copy > p:not(.events-eyebrow) { font-size: 0.84rem; }
        .events-promotion-copy .events-button { min-height: 42px; margin-top: 14px; font-size: 0.8rem; }
        .events-promotion-ornament { display: none; }
        .events-final-cta { display: block; min-height: 0; margin-top: 54px; padding: 32px 20px; }
        .events-final-copy { display: block; text-align: center; }
        .events-final-copy h2 { font-size: 2rem; }
        .events-final-copy > p:not(.events-eyebrow) { font-size: 0.88rem; }
        .events-final-actions { display: grid; width: min(320px, 100%); grid-template-columns: 1fr; margin: 18px auto 0; }
        .events-final-actions .events-button { min-height: 42px; padding: 10px 14px; font-size: 0.8rem; }
        .events-ornament { top: 12px; width: 64px; height: 84px; transform: none; }
        .events-ornament--left { left: 4px; }
        .events-ornament--right { top: auto; right: 4px; bottom: 12px; }
    }
</style>

@endsection

