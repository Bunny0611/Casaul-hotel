@extends('app')

@section('content')

@php
    $categoryMeta = [
        ['key' => 'Breakfast', 'label' => 'BREAKFAST', 'icon' => 'fa-bread-slice', 'intro_title' => 'Our Morning Favorites', 'intro_text' => 'Start your day with fresh ingredients and classic flavors, served with warm hospitality.'],
        ['key' => 'Appetizer', 'label' => 'APPETIZERS', 'icon' => 'fa-seedling', 'intro_title' => 'Light Bites & Finger Foods', 'intro_text' => 'Perfect starters for every moment, made with vibrant flavors and thoughtful presentation.'],
        ['key' => 'Main Course', 'label' => 'MAIN COURSE', 'icon' => 'fa-drumstick-bite', 'intro_title' => 'House Favorites', 'intro_text' => 'Satisfying dishes prepared with comfort, quality ingredients, and Casaul warmth.'],
        ['key' => 'Soup', 'label' => 'SOUP', 'icon' => 'fa-mug-hot', 'intro_title' => 'Comfort in Every Bowl', 'intro_text' => 'Hearty and nourishing soups crafted to soothe and delight.'],
        ['key' => 'Salad', 'label' => 'SALAD', 'icon' => 'fa-leaf', 'intro_title' => 'Fresh & Balanced', 'intro_text' => 'Crisp, vibrant salads designed to brighten every table.'],
        ['key' => 'Dessert', 'label' => 'DESSERT', 'icon' => 'fa-cake-candles', 'intro_title' => 'Sweet Finishes', 'intro_text' => 'A gentle end to the meal with comforting flavors and elegant touches.'],
        ['key' => 'Beverage', 'label' => 'BEVERAGES', 'icon' => 'fa-glass-water', 'intro_title' => 'Signature Sips', 'intro_text' => 'Refreshing drinks and house favorites made to pair perfectly with each course.'],
    ];

    $requestedCategory = request()->query('category', 'Breakfast');
    $selectedCategory = collect($categoryMeta)->first(fn ($item) => strtolower($item['key']) === strtolower((string) $requestedCategory), $categoryMeta[0])['key'];
    $selectedMeals = $menuByCategory[$selectedCategory] ?? collect();
    $selectedMeta = collect($categoryMeta)->first(fn ($item) => $item['key'] === $selectedCategory, $categoryMeta[0]);
@endphp

<main class="dining-page">
    <div class="dining-shell">
        <section class="dining-hero" aria-label="CASAUL dining hero section">
            <div class="dining-hero-copy">
                <div class="dining-hero-kicker">
                    <span class="dining-hero-kicker-line" aria-hidden="true"></span>
                    <span>CULINARY EXPERIENCES</span>
                </div>
                <h1>Dining at Casaul</h1>
                <p class="dining-hero-subtitle">A taste of comfort, crafted with care.</p>
                <div class="dining-hero-rule" aria-hidden="true"></div>
            </div>

            <div class="dining-hero-image">
                <img src="{{ asset('image/HM.jpg') }}" alt="Elegant dining table with warm lighting at Casaul Hotel">
            </div>
        </section>

        <section class="dining-intro" aria-label="Dining introduction section">
            <div class="dining-botanical dining-botanical-left" aria-hidden="true"></div>
            <div class="dining-botanical dining-botanical-right" aria-hidden="true"></div>

            <p class="dining-intro-label">A TABLE FOR EVERY MOMENT</p>
            <p class="dining-intro-copy">
                From leisurely breakfasts to satisfying Filipino favorites,
                discover dishes prepared to make every stay at Casaul memorable.
            </p>
            <div class="dining-intro-rule" aria-hidden="true"></div>
        </section>

        <section class="dining-menu-section" id="menu">
            <div class="dining-category-nav" aria-label="Dining categories">
                @foreach($categoryMeta as $category)
                    <button
                        type="button"
                        class="dining-category-btn {{ $category['key'] === $selectedCategory ? 'active' : '' }}"
                        data-dining-category="{{ $category['key'] }}"
                        aria-pressed="{{ $category['key'] === $selectedCategory ? 'true' : 'false' }}"
                    >
                        <i class="fas {{ $category['icon'] }}" aria-hidden="true"></i>
                        <span>{{ $category['label'] }}</span>
                    </button>
                @endforeach
            </div>

            <div class="dining-catalog">
                <aside class="dining-spotlight">
                    <div class="dining-spotlight-rule" aria-hidden="true"></div>
                    <h2 id="diningMenuTitle">{{ $selectedMeta['intro_title'] }}</h2>
                    <p id="diningMenuText">{{ $selectedMeta['intro_text'] }}</p>
                </aside>

                <div class="dining-card-grid" id="dining-menu-items" data-category="{{ $selectedCategory }}">
                    @forelse($selectedMeals as $meal)
                        <article class="dining-menu-card" data-category="{{ $selectedCategory }}" data-name="{{ $meal->name }}" data-price="{{ $meal->price }}" data-dining-id="{{ $meal->id }}" data-schedule="{{ $meal->diningSchedule?->period ?? '' }}">
                            <img src="{{ $meal->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($meal->image) ? secure_asset('storage/' . ltrim($meal->image, '/')) : asset('image/HM.jpg') }}" alt="{{ $meal->name }}">
                            <div class="dining-menu-card-body">
                                <h3>{{ $meal->name }}</h3>
                                <p>{{ $meal->description ?: 'A delicious option crafted for your stay.' }}</p>
                                <div class="dining-card-bottom">
                                    <span class="dining-price">₱{{ number_format((float) $meal->price, 0) }}</span>
                                    <a href="{{ route('reservation') }}" class="dining-card-link">VIEW DISH <span aria-hidden="true">→</span></a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="dining-empty-state">No dishes are currently available in this category.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="dining-featured" aria-label="Casaul signature dining section">
            <div class="dining-featured-image">
                <img src="{{ asset('image/HM.jpg') }}" alt="Warmly lit Casaul restaurant dining room">
            </div>

            <div class="dining-featured-copy">
                <p class="dining-featured-kicker">CASAUL SIGNATURE</p>
                <h2>Made for memorable moments</h2>
                <p>Discover our carefully selected dishes, prepared with fresh ingredients and the warm hospitality of Casaul Hotel.</p>
                <a href="{{ route('reservation') }}" class="dining-featured-button">EXPLORE OUR MENU <span aria-hidden="true">→</span></a>
            </div>

            <div class="dining-featured-ornament" aria-hidden="true"></div>
        </section>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryButtons = document.querySelectorAll('.dining-category-btn');
        const menuContainer = document.getElementById('dining-menu-items');
        const menuTitle = document.getElementById('diningMenuTitle');
        const menuText = document.getElementById('diningMenuText');

        const categoryCopy = {
            Breakfast: {
                title: 'Our Morning Favorites',
                text: 'Start your day with fresh ingredients and classic flavors, served with warm hospitality.'
            },
            Appetizer: {
                title: 'Light Bites & Finger Foods',
                text: 'Perfect starters for every moment, made with vibrant flavors and thoughtful presentation.'
            },
            'Main Course': {
                title: 'House Favorites',
                text: 'Satisfying dishes prepared with comfort, quality ingredients, and Casaul warmth.'
            },
            Soup: {
                title: 'Comfort in Every Bowl',
                text: 'Hearty and nourishing soups crafted to soothe and delight.'
            },
            Salad: {
                title: 'Fresh & Balanced',
                text: 'Crisp, vibrant salads designed to brighten every table.'
            },
            Dessert: {
                title: 'Sweet Finishes',
                text: 'A gentle end to the meal with comforting flavors and elegant touches.'
            },
            Beverage: {
                title: 'Signature Sips',
                text: 'Refreshing drinks and house favorites made to pair perfectly with each course.'
            }
        };

        const renderMenuItems = (items, categoryName) => {
            if (!menuContainer) return;

            menuContainer.innerHTML = '';
            menuContainer.dataset.category = categoryName;

            const copy = categoryCopy[categoryName] || categoryCopy.Breakfast;
            if (menuTitle) menuTitle.textContent = copy.title;
            if (menuText) menuText.textContent = copy.text;

            categoryButtons.forEach((button) => {
                const isActive = button.dataset.diningCategory === categoryName;
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-pressed', String(isActive));
            });

            if (!items || items.length === 0) {
                menuContainer.innerHTML = '<div class="dining-empty-state">No dishes are currently available in this category.</div>';
                return;
            }

            items.forEach((meal) => {
                const article = document.createElement('article');
                article.className = 'dining-menu-card';
                article.dataset.category = categoryName;
                article.dataset.name = meal.name;
                article.dataset.price = meal.price;
                article.dataset.diningId = meal.id;
                article.dataset.schedule = meal.schedule || '';

                const image = document.createElement('img');
                image.src = meal.image || '{{ asset('image/HM.jpg') }}';
                image.alt = meal.name;

                const body = document.createElement('div');
                body.className = 'dining-menu-card-body';

                const title = document.createElement('h3');
                title.textContent = meal.name;

                const description = document.createElement('p');
                description.textContent = meal.description || 'A delicious option crafted for your stay.';

                const bottom = document.createElement('div');
                bottom.className = 'dining-card-bottom';

                const price = document.createElement('span');
                price.className = 'dining-price';
                price.textContent = '₱' + Number(meal.price || 0).toLocaleString('en-US');

                const link = document.createElement('a');
                link.href = '{{ route('reservation') }}';
                link.className = 'dining-card-link';
                link.innerHTML = 'VIEW DISH <span aria-hidden="true">→</span>';

                bottom.appendChild(price);
                bottom.appendChild(link);
                body.appendChild(title);
                body.appendChild(description);
                body.appendChild(bottom);
                article.appendChild(image);
                article.appendChild(body);
                menuContainer.appendChild(article);
            });
        };

        categoryButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const category = this.dataset.diningCategory;

                fetch("{{ secure_url('/dining/menu') }}?category=" + encodeURIComponent(category))
                    .then((response) => response.ok ? response.json() : Promise.reject())
                    .then((payload) => renderMenuItems(payload.items || [], payload.category || category))
                    .catch(() => {
                        const fallbackItems = Array.from(document.querySelectorAll('.dining-menu-card')).filter((card) => card.dataset.category === category).map((card) => ({
                            id: card.dataset.diningId,
                            name: card.dataset.name,
                            description: card.querySelector('p')?.textContent || '',
                            price: Number(card.dataset.price || 0),
                            image: card.querySelector('img')?.src || '{{ asset('image/HM.jpg') }}',
                            schedule: card.dataset.schedule || ''
                        }));
                        renderMenuItems(fallbackItems, category);
                    });
            });
        });
    });
</script>

@endsection

