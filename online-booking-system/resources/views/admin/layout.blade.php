<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CASAUL Hotel Management - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
       
         .sidebar {
    background-color: #800000 !important;
    background-image: none !important;
    border: 0 !important;
    outline: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}
.sidebar nav,
.sidebar .mt-auto,
.sidebar .p-6 {
    background: transparent !important;
}
.sidebar nav {
    position: static;
    top: auto;
    left: auto;
    right: auto;
    width: 100%;
    gap: 1.125rem;
    padding: 0 0.75rem;
    z-index: auto;
    box-shadow: none;
    border: 0;
    border-radius: 0;
    backdrop-filter: none;
}
.sidebar nav a::after {
    content: none;
    display: none;
}
.room-pagination {
    position: static;
}
.room-pagination {
    top: auto;
    width: auto;
    padding: 0;
    background: transparent;
    box-shadow: none;
    border: 0;
    backdrop-filter: none;
    z-index: auto;
}
        
        .header {
            background: linear-gradient(90deg, #ff6b35 0%, #ff8c42 100%);
        }

        .admin-header-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto auto;
            align-items: center;
            gap: 0.75rem 1rem;
        }

        .admin-header-title { grid-column: 1; }
        .admin-header-search { grid-column: 2; }
        .admin-header-notifications { grid-column: 3; }
        .admin-header-profile { grid-column: 4; }
        .admin-header-profile .profile-dropdown-arrow { font-size: 0.7rem; }

        .admin-notification-toggle {
            position: relative;
            display: inline-flex;
            width: 2.75rem;
            height: 2.75rem;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 0.5rem;
            background: rgba(255,255,255,0.2);
            color: #fff;
            cursor: pointer;
        }

        .admin-notification-badge {
            position: absolute;
            top: -0.2rem;
            right: -0.2rem;
            display: inline-flex;
            min-width: 1.05rem;
            height: 1.05rem;
            align-items: center;
            justify-content: center;
            padding: 0 0.2rem;
            border: 2px solid #ff7b42;
            border-radius: 9999px;
            background: #dc2626;
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            line-height: 1;
        }

        .admin-notification-panel {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            z-index: 60;
            width: min(23rem, calc(100vw - 1.5rem));
            overflow: hidden;
            border: 1px solid rgba(148,163,184,0.35);
            border-radius: 0.75rem;
            background: #fff;
            color: #1f2937;
            box-shadow: 0 18px 60px rgba(15,23,42,0.22);
        }

        .admin-notification-panel[hidden] { display: none; }
        .admin-notification-header { display:flex; align-items:center; justify-content:space-between; gap:0.75rem; padding:0.8rem 1rem; border-bottom:1px solid #e5e7eb; background:#fff7ed; }
        .admin-notification-list { max-height:23rem; overflow-y:auto; }
        .admin-notification-item { display:block; width:100%; padding:0.8rem 0.9rem; border:0; border-bottom:1px solid #f3f4f6; background:#fff; text-align:left; cursor:pointer; }
        .admin-notification-item:hover, .admin-notification-item.unread { background:#fff7ed; }
        .admin-notification-item strong { display:block; color:#111827; }
        .admin-notification-item span { display:block; margin-top:0.2rem; color:#4b5563; font-size:0.8rem; line-height:1.4; }
        .admin-notification-item small { display:block; margin-top:0.3rem; color:#6b7280; font-size:0.7rem; }
        .admin-notification-empty { padding:1rem; color:#6b7280; text-align:center; }

        .admin-profile-menu {
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

        .admin-profile-menu button {
            display: flex;
            width: 100%;
            min-height: 38px;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4375rem 0.75rem;
            border: 0;
            border-radius: 0.35rem;
            background: transparent;
            color: inherit;
            text-align: left;
            cursor: pointer;
        }

        .admin-profile-menu button:hover,
        .admin-profile-menu button:focus-visible { background: #f3f4f6; }
        .admin-profile-menu[hidden] { display: none; }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        .nav-item {
            display: flex !important;
            width: 100%;
            justify-content: flex-start;
            align-items: center;
            margin: 0.08rem 0;
            border: 0 !important;
            border-radius: 0 !important;
            outline: 0 !important;
            box-shadow: none !important;
            transition: all 0.25s ease;
            color: rgba(255,255,255,0.9);
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.14);
            transform: none;
            box-shadow: none;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.2);
            border-left: 0;
        }

        .nav-item:focus-visible {
            background: rgba(255,255,255,0.3);
        }

        .nav-item i {
            width: 1.5rem;
            flex-shrink: 0;
        }

        .sidebar-notification-badge {
            display: inline-flex;
            min-width: 1.15rem;
            height: 1.15rem;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            padding: 0 0.25rem;
            border-radius: 9999px;
            background: #dc2626;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 1;
            flex-shrink: 0;
        }
        .sidebar-notification-badge[hidden] { display: none; }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 767px) {
            .sidebar {
                width: min(80vw, 18rem);
            }

            .admin-header-layout {
                grid-template-columns: minmax(0, 1fr) auto auto;
                column-gap: 0.5rem;
            }

            .admin-header-search {
                grid-column: 1 / -1;
                grid-row: 2;
            }

            .admin-header-profile {
                grid-column: 3;
                grid-row: 1;
            }

            .admin-header-notifications { grid-column: 2; grid-row: 1; }

            .header-search {
                width: 100%;
            }

            .main-content-panel {
                padding: 1rem;
            }
        }

        html, body { max-width: 100%; overflow-x: hidden; }
        .main-content-panel { min-width: 0; overflow-x: auto; }
        .main-content-panel > * { min-width: 0; max-width: 100%; }
        .main-content-panel .overflow-x-auto { max-width: 100%; -webkit-overflow-scrolling: touch; }
        .main-content-panel table { min-width: 42rem; }
        .admin-modal-panel { max-width: calc(100vw - 2rem); }

        @media (max-width: 640px) {
            .main-content-panel { padding: 0.75rem; }
            .header { padding: 0.75rem; }
            .header h2 { max-width: calc(100vw - 4rem); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .main-content-panel h2 { font-size: 1.5rem; line-height: 2rem; }
            .main-content-panel .p-6 { padding: 1rem; }
            .main-content-panel .px-6 { padding-left: 1rem; padding-right: 1rem; }
            .main-content-panel .tab-button { flex: 1 1 calc(50% - 0.5rem); min-width: 0; padding-left: 0.75rem; padding-right: 0.75rem; }
            .admin-modal-panel { max-height: calc(100vh - 1rem); max-width: calc(100vw - 1rem); }
        }
        
        .status-available { background: #10b981; }
        .status-occupied { background: #ef4444; }
        .status-maintenance { background: #f59e0b; }
        .status-reserved { background: #2563eb; }
        
        .status-pending { background: #f59e0b; }
        .status-confirmed { background: #10b981; }
        .status-checked-in { background: #06b6d4; color: #fff; }
        .status-cancelled { background: #ef4444; }
        .status-completed { background: #3b82f6; }

        /* Minimal dark-mode overrides when `dark` class is present on documentElement/body */
        .dark body, body.dark { background-color: #0b1220; color: #e6eef8; }
        body.dark .bg-white, body.dark .bg-slate-50, body.dark .bg-gray-50 { background-color: #0f1724 !important; }
        body.dark .text-gray-900, body.dark .text-slate-900, body.dark .text-gray-800 { color: #e6eef8 !important; }
        body.dark .text-gray-500, body.dark .text-slate-500 { color: #9ca3af !important; }
        body.dark .border-gray-200, body.dark .border-slate-200, body.dark .border-gray-50 { border-color: #1f2937 !important; }
        body.dark input, body.dark textarea { background-color: #071023; color: #e6eef8 !important; border-color: #1f2937 !important; }
        body.dark .header { background: linear-gradient(90deg, #0f1724 0%, #0b1220 100%); }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 text-white overflow-y-auto transform -translate-x-full transition-transform duration-300 md:translate-x-0 md:flex-shrink-0 md:overflow-y-auto flex flex-col">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider">CASAUL</h1>
                <p class="text-sm text-gray-300 mt-1">Hotel Management</p>
            </div>
            
            @php($sidebarNotificationCounts = \App\Support\StaffNotificationService::unreadSidebarCounts(auth()->user()))
            <nav class="mt-6 flex flex-col px-3">
                <a href="{{ route('admin.dashboard') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin') || request()->is('admin/') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reservations') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/reservations') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check w-6"></i>
                    <span>Reservations</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="reservations" data-module-label="reservations" aria-label="{{ $sidebarNotificationCounts['reservations'] }} unread reservations" @if($sidebarNotificationCounts['reservations'] === 0) hidden @endif>{{ $sidebarNotificationCounts['reservations'] > 9 ? '9+' : ($sidebarNotificationCounts['reservations'] ?: '') }}</span>
                </a>
                <a href="{{ route('admin.calendar') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/calendar') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span>Calendar</span>
                </a>
                <a href="{{ route('admin.refunds') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/refunds') ? 'active' : '' }}">
                    <i class="fas fa-rotate-left w-6"></i>
                    <span>Refund History</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="refunds" data-module-label="refunds" aria-label="{{ $sidebarNotificationCounts['refunds'] }} unread refunds" @if($sidebarNotificationCounts['refunds'] === 0) hidden @endif>{{ $sidebarNotificationCounts['refunds'] > 9 ? '9+' : ($sidebarNotificationCounts['refunds'] ?: '') }}</span>
                </a>
                <a href="{{ route('admin.rooms') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/rooms') ? 'active' : '' }}">
                    <i class="fas fa-bed w-6"></i>
                    <span>Rooms</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="rooms" data-module-label="room updates" aria-label="{{ $sidebarNotificationCounts['rooms'] }} unread room updates" @if($sidebarNotificationCounts['rooms'] === 0) hidden @endif>{{ $sidebarNotificationCounts['rooms'] > 9 ? '9+' : ($sidebarNotificationCounts['rooms'] ?: '') }}</span>
                </a>
                <a href="{{ route('admin.manage-account') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/manage-account') ? 'active' : '' }}">
                    <i class="fas fa-user-cog w-6"></i>
                    <span>Manage Account</span>
                </a>
                <a href="{{ route('admin.messages') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/messages') ? 'active' : '' }}">
                    <i class="fas fa-comments w-6"></i>
                    <span>Messages</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="messages" data-module-label="messages" aria-label="{{ $sidebarNotificationCounts['messages'] }} unread messages" @if($sidebarNotificationCounts['messages'] === 0) hidden @endif>{{ $sidebarNotificationCounts['messages'] > 9 ? '9+' : ($sidebarNotificationCounts['messages'] ?: '') }}</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar w-6"></i>
                    <span>Reports</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/settings') ? 'active' : '' }}">
                    <i class="fas fa-cog w-6"></i>
                    <span>Settings</span>
                </a>
            </nav>
            
        </aside>
        
        <!-- Sidebar backdrop -->
        <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden md:ml-64">
            <!-- Header -->
            <header class="header text-white px-4 py-4 sm:px-6 shadow-lg">
                <div class="admin-header-layout">
                    <div class="admin-header-title flex min-w-0 items-center gap-3">
                        <button id="sidebarToggle" class="md:hidden p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl font-semibold">Welcome, {{ auth()->user()->name }}!</h2>
                        </div>
                    </div>
                    <div class="relative header-search admin-header-search w-full sm:w-64">
                        <input id="adminHeaderSearch" type="search" placeholder="Search..." aria-label="Search this page" autocomplete="off" class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 w-full">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>
                    </div>
                    <div class="admin-header-notifications relative">
                        <button id="adminNotificationToggle" type="button" class="admin-notification-toggle" aria-label="Notifications" aria-haspopup="true" aria-expanded="false" aria-controls="adminNotificationPanel">
                            <i class="fas fa-bell"></i>
                            <span id="adminNotificationBadge" class="admin-notification-badge" hidden>0</span>
                        </button>
                        <div id="adminNotificationPanel" class="admin-notification-panel" role="menu" hidden>
                            <div class="admin-notification-header">
                                <strong>Notifications</strong>
                                <button id="adminMarkAllNotificationsRead" type="button" class="text-xs font-medium text-orange-600 hover:text-orange-700">Mark all as read</button>
                            </div>
                            <div id="adminNotificationList" class="admin-notification-list"></div>
                        </div>
                    </div>
                    <div class="admin-header-profile relative">
                        <button id="adminProfileToggle" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="adminProfileMenu" class="flex items-center justify-center gap-2 rounded-lg bg-white/20 px-3 py-2 text-sm text-white transition-colors hover:bg-white/30 sm:text-base">
                            <i class="fas fa-user-circle text-xl sm:text-2xl"></i>
                            <span class="font-medium truncate max-w-[8rem] sm:max-w-none">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down profile-dropdown-arrow" aria-hidden="true"></i>
                        </button>
                        <div id="adminProfileMenu" class="admin-profile-menu" role="menu" hidden>
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
            
            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 main-content-panel">
                @if(session('success'))
                    <div class="bg-green-500 text-white px-6 py-3 rounded-lg mb-6 animate-fade-in">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    
    <script>
        // Dynamic animations and mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s ease';
                });
            });

            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggle = document.getElementById('sidebarToggle');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
            }

            toggle.addEventListener('click', openSidebar);
            backdrop.addEventListener('click', closeSidebar);

            const profileToggle = document.getElementById('adminProfileToggle');
            const profileMenu = document.getElementById('adminProfileMenu');

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

            const notificationToggle = document.getElementById('adminNotificationToggle');
            const notificationBadge = document.getElementById('adminNotificationBadge');
            const notificationPanel = document.getElementById('adminNotificationPanel');
            const notificationList = document.getElementById('adminNotificationList');
            const notificationFeedUrl = @json(route('admin.notification-feed.index'));
            const markNotificationReadUrl = @json(route('admin.notification-feed.mark-read', ['id' => '__ID__']));
            const markAllNotificationsReadUrl = @json(route('admin.notification-feed.mark-all-read'));

            function updateNotificationBadge(count) {
                const unreadCount = Number(count) || 0;
                notificationBadge.hidden = unreadCount === 0;
                notificationBadge.textContent = unreadCount > 9 ? '9+' : String(unreadCount);
                notificationToggle.setAttribute('aria-label', unreadCount ? `${unreadCount} unread notifications` : 'Notifications');
            }

            function updateSidebarNotificationBadges(counts) {
                if (!counts) return;

                document.querySelectorAll('[data-sidebar-module]').forEach((badge) => {
                    const count = Number(counts[badge.dataset.sidebarModule]) || 0;
                    badge.hidden = count === 0;
                    badge.textContent = count > 9 ? '9+' : (count ? String(count) : '');
                    badge.setAttribute('aria-label', `${count} unread ${badge.dataset.moduleLabel}`);
                });
            }

            function persistNotificationTarget(targetUrl) {
                if (!targetUrl) return;

                try {
                    const url = new URL(targetUrl, window.location.origin);
                    const roomId = url.searchParams.get('room_id');
                    const reservationId = url.searchParams.get('reservation_id');
                    const reservationType = url.searchParams.get('reservation_type');
                    const reservationTab = url.searchParams.get('tab');
                    const messageId = url.searchParams.get('message_id');

                    if (roomId) sessionStorage.setItem('admin-notification-highlight-room-id', roomId);
                    if (reservationId) sessionStorage.setItem('admin-notification-highlight-reservation-id', reservationId);
                    if (reservationType) sessionStorage.setItem('admin-notification-highlight-reservation-type', reservationType);
                    if (reservationTab) sessionStorage.setItem('admin-notification-highlight-reservation-tab', reservationTab);
                    if (messageId) sessionStorage.setItem('admin-notification-highlight-message-id', messageId);
                } catch (error) {
                    console.warn('Could not queue admin notification target:', error);
                }
            }

            async function loadAdminNotifications() {
                try {
                    const response = await fetch(notificationFeedUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                    if (!response.ok) return;
                    const result = await response.json();
                    updateNotificationBadge(result.unread_count);
                    updateSidebarNotificationBadges(result.module_counts);
                    notificationList.replaceChildren();

                    if (!result.data.length) {
                        const empty = document.createElement('div');
                        empty.className = 'admin-notification-empty';
                        empty.textContent = 'No notifications yet.';
                        notificationList.append(empty);
                        return;
                    }

                    result.data.forEach((item) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = `admin-notification-item${item.is_read ? '' : ' unread'}`;
                        const title = document.createElement('strong');
                        title.textContent = item.title || 'Notification';
                        const message = document.createElement('span');
                        message.textContent = item.message || '';
                        const created = document.createElement('small');
                        created.textContent = item.created_at ? new Date(item.created_at).toLocaleString() : '';
                        button.append(title, message, created);
                        button.addEventListener('click', async () => {
                            try {
                                const response = await fetch(markNotificationReadUrl.replace('__ID__', encodeURIComponent(item.id)), {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, Accept: 'application/json' },
                                    credentials: 'same-origin',
                                });
                                if (response.ok) {
                                    const state = await response.json();
                                    updateNotificationBadge(state.unread_count);
                                }
                            } catch (error) {
                                console.error('Admin notification could not be marked read:', error);
                            }

                            if (item.url) {
                                persistNotificationTarget(item.url);
                                window.location.assign(item.url);
                            } else {
                                loadAdminNotifications();
                            }
                        });
                        notificationList.append(button);
                    });
                } catch (error) {
                    console.error('Admin notifications could not be loaded:', error);
                }
            }

            if (notificationToggle && notificationPanel && notificationList) {
                notificationToggle.addEventListener('click', () => {
                    notificationPanel.hidden = !notificationPanel.hidden;
                    notificationToggle.setAttribute('aria-expanded', String(!notificationPanel.hidden));
                    if (!notificationPanel.hidden) loadAdminNotifications();
                });
                document.addEventListener('click', (event) => {
                    if (!notificationToggle.contains(event.target) && !notificationPanel.contains(event.target)) {
                        notificationPanel.hidden = true;
                        notificationToggle.setAttribute('aria-expanded', 'false');
                    }
                });
                document.getElementById('adminMarkAllNotificationsRead')?.addEventListener('click', async () => {
                    const response = await fetch(markAllNotificationsReadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, Accept: 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (response.ok) loadAdminNotifications();
                });
                loadAdminNotifications();
                window.setInterval(loadAdminNotifications, 30000);
            }

            const searchInput = document.getElementById('adminHeaderSearch');
            const contentPanel = document.querySelector('.main-content-panel');
            let noResultsMessage = null;

            function showSearchMessage(show) {
                if (!contentPanel) return;

                if (show && !noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.className = 'admin-search-empty mb-6 rounded-lg border border-gray-200 bg-white px-6 py-4 text-sm text-gray-500';
                    noResultsMessage.textContent = 'No matching results found on this page.';
                    contentPanel.insertBefore(noResultsMessage, contentPanel.querySelector('.room-management-page') || contentPanel.firstElementChild);
                }

                if (noResultsMessage) {
                    noResultsMessage.classList.toggle('hidden', !show);
                }
            }

            function filterAdminPage() {
                if (!searchInput || !contentPanel) return;

                const query = searchInput.value.trim().toLowerCase();
                const rows = Array.from(contentPanel.querySelectorAll('table tbody tr'));
                const searchableCards = rows.length ? [] : Array.from(contentPanel.querySelectorAll('[data-search]'));

                if (!query) {
                    rows.forEach(row => row.classList.remove('hidden'));
                    searchableCards.forEach(card => card.classList.remove('hidden'));
                    showSearchMessage(false);
                    return;
                }

                const matchingRows = rows.filter(row => {
                    const matches = row.textContent.toLowerCase().includes(query);
                    row.classList.toggle('hidden', !matches);
                    return matches;
                });

                const matchingCards = searchableCards.filter(card => {
                    const matches = (card.dataset.search || card.textContent).toLowerCase().includes(query);
                    card.classList.toggle('hidden', !matches);
                    return matches;
                });

                showSearchMessage(matchingRows.length === 0 && matchingCards.length === 0);
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterAdminPage);
                searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        filterAdminPage();
                    }
                });
            }
        });
        // Apply persisted theme across all pages
        (function() {
            const root = document.documentElement;
            const saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                root.classList.add('dark');
                document.body.classList.add('dark');
            }

            // listen for changes from other tabs
            window.addEventListener('storage', (e) => {
                if (e.key === 'theme') {
                    if (e.newValue === 'dark') {
                        root.classList.add('dark'); document.body.classList.add('dark');
                    } else if (e.newValue === 'light') {
                        root.classList.remove('dark'); document.body.classList.remove('dark');
                    }
                }
            });
        })();
    </script>
</body>
</html>
