@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'ABOUT US',
    'subtitle' => 'A warm and welcoming retreat in the heart of Tabaco City, where comfort, hospitality, and local charm come together.',
    'cta' => ['href' => '#about-us', 'label' => 'Discover More'],
    'backgroundImage' => 'image/Royal-Suite-room.jpg',
])

<section class="about-us-section" id="about-us" style="padding: 80px 20px; background: #f8f7f3;">
    <div style="max-width: 1100px; margin: 0 auto; display: grid; gap: 40px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,0.08);">
                <h3 style="font-size: 1.3rem; margin-bottom: 10px; color: #8b5e3c;">Our Story</h3>
                <p style="line-height: 1.8; color: #555;">
                    CASAUL Hotel is a proud hospitality destination rooted in Filipino warmth and service excellence. From our first welcome to your last goodbye, we aim to make every stay memorable and meaningful.
                </p>
            </div>

            <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,0.08);">
                <h3 style="font-size: 1.3rem; margin-bottom: 10px; color: #8b5e3c;">Mission</h3>
                <p style="line-height: 1.8; color: #555;">
                    To provide exceptional hospitality through comfort, sincere service, and thoughtful experiences that make every guest feel at home.
                </p>
            </div>

            <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,0.08);">
                <h3 style="font-size: 1.3rem; margin-bottom: 10px; color: #8b5e3c;">Vision</h3>
                <p style="line-height: 1.8; color: #555;">
                    To become one of the most trusted and admired hotel brands in the region, known for quality, hospitality, and lasting guest relationships.
                </p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
            <div style="background: #fff; padding: 25px; border-radius: 14px; border: 1px solid #e8e2d8;">
                <h4 style="color: #2f3e46; margin-bottom: 8px;">Comfort</h4>
                <p style="line-height: 1.7; color: #666;">Thoughtfully designed rooms and modern amenities for a relaxing stay.</p>
            </div>
            <div style="background: #fff; padding: 25px; border-radius: 14px; border: 1px solid #e8e2d8;">
                <h4 style="color: #2f3e46; margin-bottom: 8px;">Hospitality</h4>
                <p style="line-height: 1.7; color: #666;">A team committed to being attentive, warm, and genuinely helpful.</p>
            </div>
            <div style="background: #fff; padding: 25px; border-radius: 14px; border: 1px solid #e8e2d8;">
                <h4 style="color: #2f3e46; margin-bottom: 8px;">Community</h4>
                <p style="line-height: 1.7; color: #666;">We celebrate local culture and support the community we proudly serve.</p>
            </div>
        </div>
    </div>
</section>

@endsection
