
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CASAUL Hotel - Housekeeping</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <style>

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Poppins', sans-serif; }


        * {
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100%;
            width: 100%;
            background: #f3f4f6;
        }

        body {
            overflow: hidden;
        }

        .housekeeping-shell {
            position: fixed;
            inset: 0;
        }

        .housekeeping-shell main .overflow-x-auto,
        .housekeeping-shell main .table-wrapper,
        .housekeeping-shell main .request-table-wrap,
        .housekeeping-shell main .room-table-wrap {
            max-width: 100%;
            min-width: 0;
            overflow-x: auto;
            overscroll-behavior-x: contain;
            scrollbar-color: #9ca3af #f1f5f9;
            scrollbar-width: auto;
        }

        .housekeeping-shell main .overflow-x-auto::-webkit-scrollbar,
        .housekeeping-shell main .table-wrapper::-webkit-scrollbar,
        .housekeeping-shell main .request-table-wrap::-webkit-scrollbar,
        .housekeeping-shell main .room-table-wrap::-webkit-scrollbar {
            height: 10px;
        }

        .housekeeping-shell main .overflow-x-auto::-webkit-scrollbar-track,
        .housekeeping-shell main .table-wrapper::-webkit-scrollbar-track,
        .housekeeping-shell main .request-table-wrap::-webkit-scrollbar-track,
        .housekeeping-shell main .room-table-wrap::-webkit-scrollbar-track {
            border-radius: 999px;
            background: #f1f5f9;
        }

        .housekeeping-shell main .overflow-x-auto::-webkit-scrollbar-thumb,
        .housekeeping-shell main .table-wrapper::-webkit-scrollbar-thumb,
        .housekeeping-shell main .request-table-wrap::-webkit-scrollbar-thumb,
        .housekeeping-shell main .room-table-wrap::-webkit-scrollbar-thumb {
            border: 2px solid #f1f5f9;
            border-radius: 999px;
            background: #9ca3af;
        }

        .housekeeping-shell main .overflow-x-auto::-webkit-scrollbar-thumb:hover,
        .housekeeping-shell main .table-wrapper::-webkit-scrollbar-thumb:hover,
        .housekeeping-shell main .request-table-wrap::-webkit-scrollbar-thumb:hover,
        .housekeeping-shell main .room-table-wrap::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        .hk-table-scrollbar {
            position: relative;
            width: 100%;
            height: 24px;
            margin: 0 0 12px;
            cursor: pointer;
            touch-action: none;
            user-select: none;
        }

        .hk-table-scrollbar[hidden] { display: none; }

        .hk-table-scrollbar::before {
            position: absolute;
            inset: 8px 0;
            border-radius: 999px;
            background: #e5e7eb;
            content: '';
        }

        .hk-table-scrollbar-thumb {
            position: absolute;
            top: 6px;
            left: 0;
            height: 12px;
            min-width: 36px;
            border: 2px solid #f1f5f9;
            border-radius: 999px;
            background: #9ca3af;
            box-sizing: border-box;
            cursor: grab;
        }

        .hk-table-scrollbar-thumb:active { cursor: grabbing; }
        .hk-table-scrollbar:focus-visible { outline: 2px solid #ff6b35; outline-offset: 1px; }

        header {
            margin: 0 !important;
            padding-top: 0;
        }


        .sidebar {
            background-color: #800000 !important;
            background-image: none !important;
        }

        .sidebar nav,
        .sidebar .mt-auto,
        .sidebar .p-6 {
            background: transparent !important;
        }

        .header {
            background: linear-gradient(
                90deg,
                #ff6b35 0%,
                #ff8c42 100%
            );
        }

        .nav-item {
            display: flex !important;
            width: 100%;
            justify-content: flex-start;
            align-items: center;
            margin: 0.2rem 0;
            border-radius: 0.9rem;
            transition: all 0.25s ease;
            color: rgba(255, 255, 255, 0.9);
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.14);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #ff6b35;
        }

        .nav-item i {
            width: 1.5rem;
            flex-shrink: 0;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .clean {
            background: #10b981;
        }

        .dirty {
            background: #ef4444;
        }

        .in-progress {
            background: #f59e0b;
        }

        .status-available {
            background: #10b981;
        }

        .status-occupied {
            background: #ef4444;
        }

        .status-maintenance {
            background: #f59e0b;
        }


        @media (max-width: 767px) {

            .header {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .header h2 {
                font-size: 1rem;
            }

            .header .relative input {
                display: none;
            }

            .header .relative i {
                position: static;
                font-size: 1rem;
            }

            .header .flex.items-center.space-x-2 span {
                display: none;
            }

            .header .flex.items-center.space-x-2 {
                padding: 0.5rem;
            }

            main {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
        }

        .housekeeping-header-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            align-items: center;
            gap: 0.75rem 1rem;
            width: 100%;
        }

        .housekeeping-header-title { grid-column: 1; min-width: 0; }
        .housekeeping-header-search { grid-column: 2; width: 16rem; }
        .housekeeping-profile-wrapper { grid-column: 3; }
        .header .housekeeping-profile-wrapper .profile-dropdown-arrow { font-size: 0.7rem; }

        .housekeeping-profile-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            z-index: 60;
            width: 100%;
            min-width: 0;
            padding: 0;
            box-sizing: border-box;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: #fff;
            color: #111827;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
        }

        .housekeeping-profile-menu button {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 0.6rem;
            min-height: 38px;
            padding: 0.4375rem 0.75rem;
            border: 0;
            border-radius: 0.35rem;
            background: transparent;
            color: inherit;
            text-align: left;
            cursor: pointer;
        }

        .housekeeping-profile-menu button:hover,
        .housekeeping-profile-menu button:focus-visible { background: #f3f4f6; }
        .housekeeping-profile-menu[hidden] { display: none; }

        @media (max-width: 767px) {
            .housekeeping-header-layout { grid-template-columns: minmax(0, 1fr) auto; }
            .housekeeping-header-title { grid-column: 1; }
            .housekeeping-header-search {
                grid-column: 1 / -1;
                grid-row: 2;
                width: 100%;
            }
            .header .housekeeping-header-search input {
                display: block;
                width: 100%;
                visibility: visible;
                padding: 0.55rem 2.5rem 0.55rem 0.9rem;
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 0.5rem;
                box-sizing: border-box;
            }
            .header .housekeeping-header-search .fa-search {
                position: absolute;
                top: 50%;
                right: 0.85rem;
                transform: translateY(-50%);
            }
            .housekeeping-profile-wrapper { grid-column: 2; grid-row: 1; }
            .housekeeping-profile-trigger { padding: 0.4rem 0.5rem; }
            .header .housekeeping-profile-trigger > .fa-user-circle { font-size: 1.5rem; }
            .housekeeping-profile-trigger span { white-space: nowrap; font-size: 0.75rem; }
            .housekeeping-header-title h2 { overflow: visible; white-space: normal; font-size: 0.875rem; line-height: 1.2; }
            .housekeeping-greeting-role { display: block; }
        }
    </style>

    </head>

<body class="bg-gray-100">

    <div class="housekeeping-shell flex h-screen overflow-hidden">


        <aside
            id="sidebar"
            class="sidebar fixed inset-y-0 left-0 z-50 w-64 text-white overflow-y-auto
                   transform -translate-x-full transition-transform duration-300
                     md:translate-x-0 md:relative md:flex-shrink-0 md:overflow-y-auto md:static flex flex-col"
        >
            <div class="p-6">

                <h1 class="text-2xl font-bold tracking-wider">
                    <i class="fas fa-hotel mr-2"></i>
                    CASAUL
                </h1>

                <p class="text-sm text-gray-300 mt-1">
                    Housekeeping Portal
                </p>

            </div>

            <nav class="mt-20 flex flex-col gap-2 px-3">

                <a
                    href="{{ route('housekeeping.dashboard') }}"
                    class="nav-item px-5 py-3.5
                    {{ request()->routeIs('housekeeping.dashboard') ? 'active' : '' }}"
                >
                    <i class="fas fa-broom w-6"></i>
                    <span>Dashboard</span>
                </a>

                <a
                    href="{{ route('housekeeping.assigned-rooms') }}"
                    class="nav-item px-5 py-3.5
                    {{ request()->routeIs('housekeeping.assigned-rooms') ? 'active' : '' }}"
                >
                    <i class="fas fa-bed w-6"></i>
                    <span>Assigned Rooms</span>
                </a>

                <a
                    href="{{ route('housekeeping.room-status-update') }}"
                    class="nav-item px-5 py-3.5
                    {{ request()->routeIs('housekeeping.room-status-update') ? 'active' : '' }}"
                >
                    <i class="fas fa-sync-alt w-6"></i>
                    <span>Room Status Update</span>
                </a>

                <a
                    href="{{ route('housekeeping.guest-requests') }}"
                    class="nav-item px-5 py-3.5
                    {{ request()->routeIs('housekeeping.guest-requests') ? 'active' : '' }}"
                >
                    <i class="fas fa-bell w-6"></i>
                    <span>Guest Requests</span>
                </a>

                <a
                    href="{{ route('housekeeping.messages') }}"
                    class="nav-item px-5 py-3.5
                    {{ request()->routeIs('housekeeping.messages') ? 'active' : '' }}"
                >
                    <i class="fas fa-comment-dots w-6"></i>
                    <span>Messages</span>
                </a>

                <a
                    href="{{ route('housekeeping.maintenance-report') }}"
                    class="nav-item px-5 py-3.5
                    {{ request()->routeIs('housekeeping.maintenance-report') ? 'active' : '' }}"
                >
                    <i class="fas fa-tools w-6"></i>
                    <span>Maintenance Report</span>
                </a>


                <a
                    href="{{ route('housekeeping.cleaning-history') }}"
                    class="nav-item px-5 py-3.5
                    {{ request()->routeIs('housekeeping.cleaning-history') ? 'active' : '' }}"
                >
                    <i class="fas fa-history w-6"></i>
                    <span>Cleaning History</span>
                </a>
            </nav>

        </aside>

        <div
            id="sidebarBackdrop"
            class="fixed inset-0 z-40 bg-black/50 opacity-0
                   pointer-events-none transition-opacity duration-300 md:hidden"
        ></div>

        <div class="flex-1 flex flex-col overflow-hidden md:ml-0">

    <header
        class="header z-30 text-white
               px-4 sm:px-6 py-3 flex items-center
               justify-between shadow-lg"
    >
                <div class="housekeeping-header-layout">

                <div class="housekeeping-header-title flex min-w-0 items-center gap-4">

                    <button
                        id="sidebarToggle"
                        type="button"
                        class="md:hidden p-2 rounded-lg
                               bg-white/20 hover:bg-white/30
                               transition-colors"
                    >
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="min-w-0">
                        <h2 class="text-xl font-semibold mt-0">
                            <span>Welcome,</span>
                            <span class="housekeeping-greeting-role">Housekeeping!</span>
                        </h2>
                    </div>

                </div>

                    <div class="housekeeping-header-search relative">

                        <input
                            type="text"
                            placeholder="Search..."
                            aria-label="Search this page"
                            class="w-full bg-white/20 text-white
                                   placeholder-gray-200
                                   px-4 py-2 rounded-lg
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-white/50"
                        >

                        <i
                            class="fas fa-search absolute right-3 top-3
                                   text-gray-200"
                        ></i>

                    </div>

                    <div class="housekeeping-profile-wrapper relative">
                        <button id="housekeepingProfileToggle" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="housekeepingProfileMenu" class="housekeeping-profile-trigger flex items-center gap-2 rounded-lg bg-white/20 px-4 py-2 text-white transition-colors hover:bg-white/30">
                            <i class="fas fa-user-circle text-2xl"></i>
                            <span class="font-medium">Housekeeping</span>
                            <i class="fas fa-chevron-down profile-dropdown-arrow" aria-hidden="true"></i>
                        </button>
                        <div id="housekeepingProfileMenu" class="housekeeping-profile-menu" role="menu" hidden>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" role="menuitem">
                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </header>

            <main class="flex-1 overflow-y-auto px-4 sm:px-6 pb-0 pt-3">

                @if(session('success'))

                    <div
                        class="bg-green-500 text-white
                               px-6 py-3 rounded-lg
                               mb-6 animate-fade-in"
                    >

                        <i class="fas fa-check-circle mr-2"></i>

                        {{ session('success') }}

                    </div>

                @endif
                @yield('content')

            </main>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const tableScrollerSelector = '.housekeeping-shell main .overflow-x-auto, .housekeeping-shell main .table-wrapper, .housekeeping-shell main .request-table-wrap:not([data-scrollbar-control]), .housekeeping-shell main .room-table-wrap';
            document.querySelectorAll(tableScrollerSelector).forEach(function (scroller, index) {
                if (scroller.dataset.scrollbarReady === 'true') return;
                scroller.dataset.scrollbarReady = 'true';
                if (!scroller.id) scroller.id = 'housekeeping-table-scroll-' + index;

                const scrollbar = document.createElement('div');
                scrollbar.className = 'hk-table-scrollbar';
                scrollbar.setAttribute('role', 'scrollbar');
                scrollbar.setAttribute('aria-label', 'Table horizontal scroll');
                scrollbar.setAttribute('aria-controls', scroller.id);
                scrollbar.setAttribute('aria-orientation', 'horizontal');
                scrollbar.setAttribute('aria-valuemin', '0');
                scrollbar.setAttribute('aria-valuemax', '100');
                scrollbar.setAttribute('aria-valuenow', '0');
                scrollbar.tabIndex = 0;

                const thumb = document.createElement('div');
                thumb.className = 'hk-table-scrollbar-thumb';
                scrollbar.appendChild(thumb);
                scroller.insertAdjacentElement('afterend', scrollbar);

                let animationFrame = 0;
                let dragging = false;
                let dragStartX = 0;
                let dragStartScroll = 0;

                function updateScrollbar() {
                    animationFrame = 0;
                    const maxScroll = scroller.scrollWidth - scroller.clientWidth;
                    if (maxScroll <= 1) {
                        scrollbar.hidden = true;
                        return;
                    }

                    scrollbar.hidden = false;
                    const trackWidth = scrollbar.clientWidth;
                    const thumbWidth = Math.min(trackWidth, Math.max(36, trackWidth * scroller.clientWidth / scroller.scrollWidth));
                    const maxThumbOffset = Math.max(0, trackWidth - thumbWidth);
                    const thumbOffset = maxScroll > 0 ? scroller.scrollLeft / maxScroll * maxThumbOffset : 0;
                    thumb.style.width = thumbWidth + 'px';
                    thumb.style.transform = 'translateX(' + thumbOffset + 'px)';
                    scrollbar.setAttribute('aria-valuemax', String(Math.round(maxScroll)));
                    scrollbar.setAttribute('aria-valuenow', String(Math.round(scroller.scrollLeft)));
                    scrollbar.setAttribute('aria-valuetext', Math.round(scroller.scrollLeft) + ' of ' + Math.round(maxScroll) + ' pixels');
                }

                function scheduleScrollbarUpdate() {
                    if (!animationFrame) animationFrame = window.requestAnimationFrame(updateScrollbar);
                }

                function scrollToPointer(clientX) {
                    const maxScroll = scroller.scrollWidth - scroller.clientWidth;
                    const maxThumbOffset = scrollbar.clientWidth - thumb.offsetWidth;
                    if (maxScroll <= 0 || maxThumbOffset <= 0) return;
                    const trackRect = scrollbar.getBoundingClientRect();
                    const thumbOffset = Math.max(0, Math.min(maxThumbOffset, clientX - trackRect.left - thumb.offsetWidth / 2));
                    scroller.scrollLeft = thumbOffset / maxThumbOffset * maxScroll;
                }

                scrollbar.addEventListener('pointerdown', function (event) {
                    if (event.button !== 0) return;
                    event.preventDefault();
                    scrollbar.setPointerCapture(event.pointerId);
                    dragging = true;
                    dragStartX = event.clientX;
                    dragStartScroll = scroller.scrollLeft;
                    if (event.target !== thumb) {
                        scrollToPointer(event.clientX);
                        dragStartScroll = scroller.scrollLeft;
                    }
                });

                scrollbar.addEventListener('pointermove', function (event) {
                    if (!dragging || !scrollbar.hasPointerCapture(event.pointerId)) return;
                    const maxScroll = scroller.scrollWidth - scroller.clientWidth;
                    const maxThumbOffset = scrollbar.clientWidth - thumb.offsetWidth;
                    if (maxScroll <= 0 || maxThumbOffset <= 0) return;
                    scroller.scrollLeft = dragStartScroll + (event.clientX - dragStartX) / maxThumbOffset * maxScroll;
                });

                function stopDragging() { dragging = false; }
                scrollbar.addEventListener('pointerup', stopDragging);
                scrollbar.addEventListener('pointercancel', stopDragging);
                scrollbar.addEventListener('scroll', scheduleScrollbarUpdate);
                scroller.addEventListener('scroll', scheduleScrollbarUpdate, { passive: true });
                scrollbar.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowRight') scroller.scrollBy({ left: scroller.clientWidth * 0.8, behavior: 'smooth' });
                    else if (event.key === 'ArrowLeft') scroller.scrollBy({ left: -scroller.clientWidth * 0.8, behavior: 'smooth' });
                    else if (event.key === 'Home') scroller.scrollTo({ left: 0, behavior: 'smooth' });
                    else if (event.key === 'End') scroller.scrollTo({ left: scroller.scrollWidth, behavior: 'smooth' });
                    else return;
                    event.preventDefault();
                });

                window.addEventListener('resize', scheduleScrollbarUpdate, { passive: true });
                if ('ResizeObserver' in window) {
                    const resizeObserver = new ResizeObserver(scheduleScrollbarUpdate);
                    resizeObserver.observe(scroller);
                    if (scroller.firstElementChild) resizeObserver.observe(scroller.firstElementChild);
                }
                if ('MutationObserver' in window) {
                    const mutationObserver = new MutationObserver(scheduleScrollbarUpdate);
                    mutationObserver.observe(scroller, { childList: true, characterData: true, subtree: true });
                }

                updateScrollbar();
            });

            document.addEventListener('submit', function (event) {

                const form = event.target;

                if (form.dataset.submitting === 'true') {
                    event.preventDefault();
                    return;
                }

                form.dataset.submitting = 'true';

                const submitButton = event.submitter ||
                    form.querySelector('button[type="submit"], input[type="submit"]');

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.setAttribute('aria-busy', 'true');
                }

                if (event.defaultPrevented) {
                    setTimeout(function () {
                        delete form.dataset.submitting;

                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.removeAttribute('aria-busy');
                        }
                    }, 0);
                }

            });

            const profileToggle = document.getElementById('housekeepingProfileToggle');
            const profileMenu = document.getElementById('housekeepingProfileMenu');

            if (profileToggle && profileMenu) {
                function closeProfileMenu() {
                    profileMenu.hidden = true;
                    profileToggle.setAttribute('aria-expanded', 'false');
                }

                profileToggle.addEventListener('click', function () {
                    profileMenu.hidden = !profileMenu.hidden;
                    profileToggle.setAttribute('aria-expanded', String(!profileMenu.hidden));
                });

                document.addEventListener('click', function (event) {
                    if (!profileToggle.parentElement.contains(event.target)) closeProfileMenu();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeProfileMenu();
                        profileToggle.focus();
                    }
                });
            }

            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggle = document.getElementById('sidebarToggle');

            if (!sidebar || !backdrop || !toggle) {
                return;
            }

            function openSidebar() {

                sidebar.classList.remove('-translate-x-full');

                backdrop.classList.remove(
                    'opacity-0',
                    'pointer-events-none'
                );

                backdrop.classList.add('opacity-100');
            }


            function closeSidebar() {

                sidebar.classList.add('-translate-x-full');

                backdrop.classList.add(
                    'opacity-0',
                    'pointer-events-none'
                );

                backdrop.classList.remove('opacity-100');
            }


            toggle.addEventListener('click', function () {
                openSidebar();
            });


            backdrop.addEventListener('click', function () {
                closeSidebar();
            });


            window.addEventListener('resize', function () {

                if (window.innerWidth >= 768) {
                    closeSidebar();
                }

            });

        });
    </script>

</body>
</html>