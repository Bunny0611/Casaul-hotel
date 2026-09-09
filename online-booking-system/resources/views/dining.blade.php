@extends('app')

@section('content')

@php
    $defaultCategory = 'Breakfast';
    $categoryButtons = [
        ['name' => 'Breakfast', 'icon' => 'fa-bread-slice'],
        ['name' => 'Appetizer', 'icon' => 'fa-seedling'],
        ['name' => 'Main Course', 'icon' => 'fa-drumstick-bite'],
        ['name' => 'Soup', 'icon' => 'fa-mug-hot'],
        ['name' => 'Salad', 'icon' => 'fa-leaf'],
        ['name' => 'Dessert', 'icon' => 'fa-cake-candles'],
        ['name' => 'Beverage', 'icon' => 'fa-glass-water'],
    ];
    $selectedCategory = $defaultCategory;
    $selectedMeals = $menuByCategory[$selectedCategory] ?? collect();
@endphp

<main class="dining-page">
    <div class="dining-container">
        <section class="dining-hero">
            <div class="dining-hero-copy">
                <p class="dining-eyebrow">Good food, great memories <span aria-hidden="true">*</span></p>
                <h1>Dining at<br><strong>CASAUL</strong> Hotel</h1>
                <div class="dining-rule"></div>
                <p class="dining-hero-text">We bring people together with warm hospitality and memorable dining experiences.</p>
            </div>
            <div class="dining-hero-image">
                <img src="{{ asset('image/HM.jpg') }}" alt="Warm dining atmosphere at CASAUL Hotel">
            </div>
        </section>

        <section class="dining-menu" id="menu">
            <header class="dining-section-heading">
                <div class="dining-heading-mark"><span></span><i class="fas fa-utensils"></i><span></span></div>
                <h2>Dining</h2>
                <p>Choose a dining experience for your stay.</p>
                <div class="dining-rule"></div>
            </header>

            <div class="dining-category-bar" aria-label="Dining categories">
                @foreach($categoryButtons as $category)
                    <button
                        type="button"
                        class="dining-category-btn {{ $category['name'] === $selectedCategory ? 'active' : '' }}"
                        data-dining-category="{{ $category['name'] }}"
                        aria-pressed="{{ $category['name'] === $selectedCategory ? 'true' : 'false' }}"
                    >
                        <i class="fas {{ $category['icon'] }}"></i>
                        <span>{{ $category['name'] }}</span>
                    </button>
                @endforeach
            </div>

            <div class="dining-menu-panel">
                <div class="dining-menu-header">
                    <h3>{{ $selectedCategory }} Menu</h3>
                </div>

                <div class="dining-grid" id="dining-menu-items" data-category="{{ $selectedCategory }}">
                    @foreach($selectedMeals as $meal)
                        @php
                            $availableFrom = $meal->available_from ?: $meal->diningSchedule?->available_from;
                            $availableTo = $meal->available_to ?: $meal->diningSchedule?->available_to;
                            $timeDisplay = $availableFrom && $availableTo ? 'Available: ' . \Carbon\Carbon::parse($availableFrom)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($availableTo)->format('g:i A') : 'Available All Day';
                        @endphp

                        <article class="dining-menu-card" data-category="{{ $selectedCategory }}" data-name="{{ $meal->name }}" data-price="{{ $meal->price }}" data-dining-id="{{ $meal->id }}" data-schedule="{{ $meal->diningSchedule?->period ?? '' }}">
                            <img src="{{ $meal->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($meal->image) ? asset('storage/' . $meal->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $meal->name }}">
                            <div class="dining-menu-card-body">
                                <h4>{{ $meal->name }}</h4>
                                <p>{{ $meal->description ?: 'A delicious option crafted for your stay.' }}</p>
                                <div class="dining-meta-tag">{{ $selectedCategory }}</div>
                                <div class="dining-time">{{ $timeDisplay }}</div>
                                <div class="dining-card-footer">
                                    <span class="dining-price">₱{{ number_format((float) $meal->price, 0) }}</span>
                                    <div class="dining-qty">
                                        <button type="button" class="qty-btn qty-decrease" data-dining-id="{{ $meal->id }}" aria-label="Decrease quantity">−</button>
                                        <input type="number" min="1" value="1" class="dining-quantity" data-dining-id="{{ $meal->id }}">
                                        <button type="button" class="qty-btn qty-increase" data-dining-id="{{ $meal->id }}" aria-label="Increase quantity">+</button>
                                    </div>
                                </div>
                                <button type="button" class="dining-add-btn select-option-btn" data-title="{{ $meal->name }}" data-price="{{ $meal->price }}" data-category="dining" data-dining-id="{{ $meal->id }}">Add to Reservation</button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dining-experience" id="dining-experience">
            <div class="dining-experience-copy">
                <header class="dining-section-heading"><h2>Dining Experience</h2><div class="dining-rule"></div></header>
                <div class="dining-benefits">
                    <article><i class="fas fa-chair"></i><div><h3>Comfortable Seating</h3><p>Relax with a friendly ambiance designed for easy conversation.</p></div></article>
                    <article><i class="fas fa-users"></i><div><h3>Family-Friendly</h3><p>Options for all tastes, perfect for group dining.</p></div></article>
                    <article><i class="fas fa-concierge-bell"></i><div><h3>Evening Atmosphere</h3><p>Enjoy a calm, welcoming vibe after a day of exploring.</p></div></article>
                </div>
                <div class="dining-booking"><i class="far fa-calendar-check"></i><div><strong>Planning a special occasion or group dining?</strong><span>We're here to make it memorable.</span></div><a href="{{ route('reservation') }}">Book a Table</a></div>
            </div>
            <img class="dining-experience-image" src="{{ asset('image/HM.jpg') }}" alt="CASAUL Hotel dining room">
        </section>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryButtons = document.querySelectorAll('.dining-category-btn');
        const menuContainer = document.getElementById('dining-menu-items');

        const formatTimeLabel = (time) => {
            if (!time) return 'Available All Day';
            const [hours, minutes] = String(time).split(':').map(Number);
            const period = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `Available: ${displayHours}:${String(minutes || 0).padStart(2, '0')} ${period}`;
        };

        const renderMenuItems = (items, categoryName) => {
            if (!menuContainer) return;

            menuContainer.innerHTML = '';
            menuContainer.dataset.category = categoryName;

            if (!items || items.length === 0) {
                menuContainer.innerHTML = '<div class="dining-empty-state">No items available in this category right now.</div>';
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

                const imageUrl = meal.image || '{{ asset('image/Royal-Suite-room.jpg') }}';
                const availableFrom = meal.available_from || meal.schedule || '';
                const availableTo = meal.available_to || '';
                const timeLabel = availableFrom && availableTo ? `Available: ${formatTimeLabel(availableFrom)} - ${formatTimeLabel(availableTo)}` : 'Available All Day';

                article.innerHTML = `
                    <img src="${imageUrl}" alt="${meal.name}">
                    <div class="dining-menu-card-body">
                        <h4>${meal.name}</h4>
                        <p>${meal.description || 'A delicious option crafted for your stay.'}</p>
                        <div class="dining-meta-tag">${categoryName}</div>
                        <div class="dining-time">${timeLabel}</div>
                        <div class="dining-card-footer">
                            <span class="dining-price">₱${Number(meal.price || 0).toLocaleString('en-US')}</span>
                            <div class="dining-qty">
                                <button type="button" class="qty-btn qty-decrease" data-dining-id="${meal.id}" aria-label="Decrease quantity">−</button>
                                <input type="number" min="1" value="1" class="dining-quantity" data-dining-id="${meal.id}">
                                <button type="button" class="qty-btn qty-increase" data-dining-id="${meal.id}" aria-label="Increase quantity">+</button>
                            </div>
                        </div>
                        <button type="button" class="dining-add-btn select-option-btn" data-title="${meal.name}" data-price="${meal.price}" data-category="dining" data-dining-id="${meal.id}">Add to Reservation</button>
                    </div>
                `;

                menuContainer.appendChild(article);
            });

            bindDiningInteractions();
        };

        const bindDiningInteractions = () => {
            document.querySelectorAll('.dining-menu-card .qty-btn').forEach((button) => {
                button.onclick = function () {
                    const card = this.closest('.dining-menu-card');
                    const input = card?.querySelector('.dining-quantity');
                    if (!input) return;
                    const currentValue = Number(input.value) || 1;
                    const nextValue = this.classList.contains('qty-increase') ? currentValue + 1 : Math.max(1, currentValue - 1);
                    input.value = nextValue;
                };
            });

            document.querySelectorAll('.dining-menu-card .dining-quantity').forEach((input) => {
                input.onchange = function () {
                    const card = this.closest('.dining-menu-card');
                    if (!card) return;
                    const value = Math.max(1, Number(this.value) || 1);
                    this.value = value;
                };
            });

            document.querySelectorAll('.dining-menu-card .dining-add-btn').forEach((button) => {
                button.onclick = function () {
                    const card = this.closest('.dining-menu-card');
                    const id = card?.dataset.diningId;
                    const quantityInput = card?.querySelector('.dining-quantity');
                    const quantity = Number(quantityInput?.value || 1);
                    const diningItem = {
                        id: Number(id),
                        title: card?.dataset.name || this.dataset.title,
                        price: Number(card?.dataset.price || this.dataset.price || 0),
                        quantity,
                        schedule: document.getElementById('diningSchedule')?.value || card?.dataset.schedule || '',
                        table: document.getElementById('diningTable')?.value || '',
                        date: document.getElementById('diningDate')?.value || '',
                    };

                    const existingIndex = selectedDining.findIndex((item) => item.id === diningItem.id);
                    if (existingIndex >= 0) {
                        selectedDining[existingIndex].quantity = quantity;
                        selectedDining[existingIndex].schedule = diningItem.schedule;
                        selectedDining[existingIndex].table = diningItem.table;
                        selectedDining[existingIndex].date = diningItem.date;
                    } else {
                        selectedDining.push(diningItem);
                    }

                    updateSummary();
                    this.textContent = 'Added';
                    setTimeout(() => { this.textContent = 'Add to Reservation'; }, 800);
                };
            });
        };

        categoryButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const category = this.dataset.diningCategory;
                categoryButtons.forEach((item) => {
                    const isActive = item === this;
                    item.classList.toggle('active', isActive);
                    item.setAttribute('aria-pressed', String(isActive));
                }, this);

                fetch(`{{ url('/dining/menu') }}?category=${encodeURIComponent(category)}`)
                    .then((response) => response.ok ? response.json() : Promise.reject())
                    .then((payload) => renderMenuItems(payload.items || [], payload.category || category))
                    .catch(() => {
                        const items = Array.from(document.querySelectorAll('.dining-menu-card')).filter((card) => card.dataset.category === category);
                        renderMenuItems(items.map((card) => ({
                            id: card.dataset.diningId,
                            name: card.dataset.name,
                            description: card.querySelector('p')?.textContent || '',
                            price: Number(card.dataset.price || 0),
                            image: card.querySelector('img')?.src || '{{ asset('image/Royal-Suite-room.jpg') }}',
                            available_from: '',
                            available_to: '',
                            schedule: card.dataset.schedule || '',
                        })), category);
                    });
            });
        });
    });
</script>

@endsection

