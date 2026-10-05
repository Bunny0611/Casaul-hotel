<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CASAUL Hotel Management - Employee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #800000 0%, #5c0000 100%);
            min-width: 10rem;
            width: min(10rem, 100%);
        }

        .header {
            background: linear-gradient(90deg, #ff6b35 0%, #ff8c42 100%);
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.9rem;
            min-height: auto;
        }

        .header .header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1 1 auto;
            min-width: 0;
        }

        .header .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 0 1 auto;
            min-width: 0;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .header .search-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1 1 220px;
            min-width: 0;
            max-width: 18rem;
            width: 100%;
            position: relative;
            transition: all 0.2s ease;
        }

        .header .search-wrapper input {
            flex: 1 1 100%;
            width: 100%;
            transition: width 0.2s ease, opacity 0.2s ease;
        }

        .mobile-search-button {
            display: none;
            border: none;
            background: rgba(255,255,255,0.18);
            color: white;
            padding: 0.55rem;
            border-radius: 999px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .header .search-wrapper.active {
            max-width: 100%;
            flex: 1 1 100%;
            width: 100%;
        }

        .header .search-wrapper.active input {
            display: block;
            opacity: 1;
            width: 100%;
        }

        .header .profile-box .font-medium {
            white-space: nowrap;
        }

        .employee-header-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 16rem auto auto;
            align-items: center;
            gap: 0.75rem 1rem;
            width: 100%;
        }

        .employee-header-title { grid-column: 1; min-width: 0; }
        .employee-header-search { grid-column: 2; width: 16rem; max-width: 16rem; }
        .employee-header-notifications { grid-column: 3; }
        .employee-header-profile { grid-column: 4; }
        .employee-profile-wrapper .profile-dropdown-arrow { font-size: 0.7rem; }

        @media (max-width: 768px) {
            .sidebar {
                width: min(18rem, 100%);
            }

            .header {
                padding: 0.65rem 0.75rem;
            }

            .header-left {
                justify-content: space-between;
                flex: 1 1 100%;
                min-width: 0;
            }

            .header-right {
                justify-content: flex-end;
                gap: 0.75rem;
                align-items: center;
                flex-wrap: nowrap;
                min-width: 0;
            }

            .header .search-wrapper {
                max-width: none;
                flex: 0 0 auto;
                width: auto;
                min-width: 0;
            }

            .header .search-wrapper input {
                display: none;
                opacity: 0;
                width: 0;
                padding: 0;
                margin: 0;
                border: none;
                visibility: hidden;
                min-width: 0;
            }

            .header .search-wrapper.active {
                flex: 1 1 auto;
                width: auto;
                max-width: calc(100% - 4.5rem);
                min-width: 0;
            }

            .header .search-wrapper.active input {
                display: block;
                opacity: 1;
                visibility: visible;
                width: 100%;
                padding: 0.55rem 0.9rem;
                border-radius: 999px;
                margin: 0;
                border: 1px solid rgba(255,255,255,0.2);
                box-sizing: border-box;
            }

            .mobile-search-button {
                display: inline-flex;
            }

            .header .profile-box {
                width: auto;
                padding: 0.35rem 0.55rem;
            }

            .header .profile-box > i { font-size: 1.25rem; }

            .header .profile-box span {
                display: inline;
                max-width: 5rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 0.75rem;
            }

            .employee-header-layout { grid-template-columns: minmax(0, 1fr) auto; column-gap: 0.5rem; row-gap: 0.5rem; }
            .employee-header-title { grid-column: 1; grid-row: 1; gap: 0.5rem; }
            .employee-header-search {
                grid-column: 1;
                grid-row: 2;
                width: 100%;
                max-width: none;
                flex: none;
            }
            .header .employee-header-search input {
                display: block;
                opacity: 1;
                visibility: visible;
                width: 100%;
                padding: 0.55rem 0.9rem;
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 0.5rem;
                box-sizing: border-box;
            }
            .employee-header-notifications { grid-column: 2; grid-row: 1; justify-self: end; }
            .employee-header-profile { grid-column: 2; grid-row: 2; justify-self: end; }
            .employee-header-title h2 {
                overflow: visible;
                overflow-wrap: anywhere;
                text-overflow: clip;
                white-space: normal;
                font-size: 0.85rem;
                line-height: 1.15;
            }
            .employee-profile-wrapper .profile-box { gap: 0.35rem; padding: 0.35rem 0.4rem; }
            .employee-profile-wrapper .profile-box span {
                max-width: 4.5rem;
                overflow: visible;
                overflow-wrap: anywhere;
                text-overflow: clip;
                white-space: normal;
                font-size: 0.75rem;
                line-height: 1.1;
            }

            .nav-item span {
                white-space: normal;
            }
        }

        .nav-item {
            display: flex;
            width: 100%;
            justify-content: flex-start;
            align-items: center;
            margin: 0.2rem 0;
            border-radius: 0.9rem;
            transition: all 0.25s ease;
            color: rgba(255, 255, 255, 0.9);
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(4px);
        }

        .nav-item.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid #ff6b35;
        }

        .nav-item i {
            width: 1.5rem;
            flex-shrink: 0;
        }

        .nav-item span {
            white-space: nowrap;
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

        .status-available { background: #10b981; color: #fff; }
        .status-occupied { background: #ef4444; color: #fff; }
        .status-maintenance { background: #f59e0b; color: #fff; }
        .status-pending { background: #f59e0b; color: #fff; }
        .status-confirmed { background: #10b981; color: #fff; }
        .status-checked-in { background: #06b6d4; color: #fff; }
        .status-cancelled { background: #ef4444; color: #fff; }
        .status-completed { background: #3b82f6; color: #fff; }
        .status-cleaning { background: #8b5cf6; color: #fff; }
        .status-dirty { background: #64748b; color: #fff; }

        .employee-profile-wrapper { position: relative; }
        .employee-profile-menu {
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

        .employee-profile-menu button {
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

        .employee-profile-menu button:hover,
        .employee-profile-menu button:focus-visible { background: #f3f4f6; }
        .employee-profile-menu[hidden] { display: none; }

        .notification-bell-button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.75rem;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            transition: all 0.2s ease;
        }

        .notification-bell-button:hover { background: rgba(255,255,255,0.26); }
        .notification-bell-button .notification-badge {
            position: absolute;
            top: -0.2rem;
            right: -0.2rem;
            min-width: 1.1rem;
            height: 1.1rem;
            border-radius: 9999px;
            background: #dc2626;
            color: #fff;
            font-size: 0.62rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.24rem;
            border: 2px solid #ff8c42;
        }
        .notification-bell-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 0.7rem);
            width: min(23rem, calc(100vw - 1.5rem));
            background: rgba(255,255,255,0.98);
            border: 1px solid rgba(148,163,184,0.35);
            border-radius: 0.9rem;
            box-shadow: 0 18px 60px rgba(15,23,42,0.22);
            color: #1f2937;
            overflow: hidden;
            z-index: 60;
        }
        .notification-bell-panel[hidden] { display: none; }
        .notification-bell-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            background: #fff7ed;
        }
        .notification-bell-list { max-height: 23rem; overflow-y: auto; }
        .notification-item {
            display: block;
            width: 100%;
            text-align: left;
            background: #fff;
            border: none;
            border-bottom: 1px solid #f3f4f6;
            padding: 0.8rem 0.9rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .notification-item:hover { background: #fff7ed; }
        .notification-item.unread { background: #fffaf5; }
        .notification-item .title { font-weight: 700; color: #111827; margin-bottom: 0.15rem; }
        .notification-item .message { font-size: 0.8rem; color: #4b5563; line-height: 1.45; }
        .notification-item .meta { font-size: 0.7rem; color: #6b7280; margin-top: 0.35rem; }
        .notification-empty { padding: 1rem; color: #6b7280; text-align: center; }
        .notification-toast-container {
            position: fixed;
            right: 1.25rem;
            bottom: 1.25rem;
            z-index: 70;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            pointer-events: none;
        }
        .notification-toast {
            width: min(22rem, calc(100vw - 2rem));
            background: rgba(255,255,255,0.98);
            border: 1px solid rgba(148,163,184,0.35);
            border-left: 4px solid #ff7b42;
            border-radius: 0.9rem;
            box-shadow: 0 20px 50px rgba(15,23,42,0.2);
            color: #1f2937;
            padding: 0.8rem 0.8rem 0.55rem;
            pointer-events: auto;
            animation: notifySlideIn 0.25s ease-out;
        }
        @keyframes notifySlideIn {
            from { transform: translateX(30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .notification-toast-header { display: flex; align-items: center; gap: 0.6rem; }
        .notification-toast .toast-close { margin-left: auto; background: transparent; border: none; color: #6b7280; font-size: 1.1rem; cursor: pointer; }
        .notification-toast .toast-title { font-weight: 700; color: #111827; }
        .notification-toast .toast-message { margin-top: 0.25rem; color: #4b5563; font-size: 0.82rem; line-height: 1.45; }
        .notification-toast .toast-action { margin-top: 0.7rem; display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 9999px; background: #ff7b42; color: white; font-weight: 600; padding: 0.45rem 0.8rem; cursor: pointer; }

        @media (max-width: 520px) {
            .notification-bell-panel {
                position: fixed;
                top: var(--notification-panel-top, 5rem);
                right: 0.5rem;
                left: 0.5rem;
                display: flex;
                width: auto;
                max-height: calc(100dvh - var(--notification-panel-top, 5rem) - env(safe-area-inset-bottom) - 0.5rem);
                flex-direction: column;
            }
            .notification-bell-header { flex: 0 0 auto; }
            .notification-bell-list { min-height: 0; max-height: none; flex: 1 1 auto; overscroll-behavior: contain; }
            .notification-toast-container { right: 0.5rem; bottom: max(0.5rem, env(safe-area-inset-bottom)); left: 0.5rem; }
            .notification-toast { width: 100%; max-height: calc(100dvh - 1rem - env(safe-area-inset-top) - env(safe-area-inset-bottom)); overflow-y: auto; box-sizing: border-box; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 text-white overflow-y-auto transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0 md:flex-shrink-0 md:overflow-y-auto md:static flex flex-col">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider">CASAUL</h1>
                <p class="text-sm text-gray-300 mt-1">Employee Portal</p>
            </div>

            @php($sidebarNotificationCounts = \App\Support\StaffNotificationService::unreadSidebarCounts(auth()->user()))
            <nav class="mt-10 flex flex-col px-3">
                <a href="{{ route('employee.dashboard') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('employee.reservation') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.reservation') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check w-6"></i>
                    <span>Reservation</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="reservations" data-module-label="reservations" aria-label="{{ $sidebarNotificationCounts['reservations'] }} unread reservations" @if($sidebarNotificationCounts['reservations'] === 0) hidden @endif>{{ $sidebarNotificationCounts['reservations'] > 9 ? '9+' : ($sidebarNotificationCounts['reservations'] ?: '') }}</span>
                </a>
                <a href="{{ route('employee.calendar') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.calendar') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span>Calendar</span>
                </a>
                <a href="{{ route('employee.refunds') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.refunds') ? 'active' : '' }}">
                    <i class="fas fa-rotate-left w-6"></i>
                    <span>Refund History</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="refunds" data-module-label="refunds" aria-label="{{ $sidebarNotificationCounts['refunds'] }} unread refunds" @if($sidebarNotificationCounts['refunds'] === 0) hidden @endif>{{ $sidebarNotificationCounts['refunds'] > 9 ? '9+' : ($sidebarNotificationCounts['refunds'] ?: '') }}</span>
                </a>
                <a href="{{ route('employee.checkin') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.checkin') ? 'active' : '' }}">
                    <i class="fas fa-sign-in-alt w-6"></i>
                    <span>Check-in/Check-out</span>
                </a>
                <a href="{{ route('employee.room-status') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.room-status') ? 'active' : '' }}">
                    <i class="fas fa-bed w-6"></i>
                    <span>Room Status</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="rooms" data-module-label="room updates" aria-label="{{ $sidebarNotificationCounts['rooms'] }} unread room updates" @if($sidebarNotificationCounts['rooms'] === 0) hidden @endif>{{ $sidebarNotificationCounts['rooms'] > 9 ? '9+' : ($sidebarNotificationCounts['rooms'] ?: '') }}</span>
                </a>
                <a href="{{ route('employee.guest-requests') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.guest-requests') ? 'active' : '' }}">
                    <i class="fas fa-hotel w-6"></i>
                    <span>Guest Requests</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="requests" data-module-label="guest requests" aria-label="{{ $sidebarNotificationCounts['requests'] }} unread guest requests" @if($sidebarNotificationCounts['requests'] === 0) hidden @endif>{{ $sidebarNotificationCounts['requests'] > 9 ? '9+' : ($sidebarNotificationCounts['requests'] ?: '') }}</span>
                </a>
                <a href="{{ route('employee.messages') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.messages') ? 'active' : '' }}">
                    <i class="fas fa-comments w-6"></i>
                    <span>Messages</span>
                    <span class="sidebar-notification-badge" data-sidebar-module="messages" data-module-label="messages" aria-label="{{ $sidebarNotificationCounts['messages'] }} unread messages" @if($sidebarNotificationCounts['messages'] === 0) hidden @endif>{{ $sidebarNotificationCounts['messages'] > 9 ? '9+' : ($sidebarNotificationCounts['messages'] ?: '') }}</span>
                </a>
            </nav>

        </aside>

        <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

        <div class="flex-1 flex min-w-0 flex-col overflow-hidden md:ml-0">
            <header class="header text-white px-6 py-4 flex items-center justify-between shadow-lg">
                <div class="employee-header-layout">
                    <div class="header-left employee-header-title">
                        <button id="sidebarToggle" class="md:hidden rounded-lg bg-white/20 p-2 transition-colors hover:bg-white/30">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="min-w-0">
                            <h2 class="text-xl font-semibold text-white truncate">@yield('pageTitle', 'Welcome, Employee!')</h2>
                        </div>
                    </div>
                    <div class="search-wrapper employee-header-search relative">
                        <input id="headerSearchInput" type="text" placeholder="Search..." class="w-full rounded-lg bg-white/20 px-4 py-2 text-white placeholder-gray-200 focus:outline-none focus:ring-2 focus:ring-white/50">
                    </div>
                    <div class="relative employee-header-notifications">
                        <button id="employeeNotificationToggle" type="button" class="notification-bell-button" aria-haspopup="true" aria-expanded="false" aria-controls="employeeNotificationPanel">
                            <i class="fas fa-bell"></i>
                            <span id="employeeNotificationBadge" class="notification-badge hidden">0</span>
                        </button>
                        <div id="employeeNotificationPanel" class="notification-bell-panel" role="menu" hidden>
                            <div class="notification-bell-header">
                                <strong>Notifications</strong>
                                <button id="employeeMarkAllRead" type="button" class="text-xs font-medium text-orange-600 hover:text-orange-700">Mark all as read</button>
                            </div>
                            <div id="employeeNotificationList" class="notification-bell-list"></div>
                        </div>
                    </div>
                    <div class="employee-profile-wrapper employee-header-profile">
                        <button id="employeeProfileToggle" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="employeeProfileMenu" class="profile-box flex items-center gap-2 rounded-lg bg-white/20 px-4 py-2 text-white transition-colors hover:bg-white/30">
                            <i class="fas fa-user-circle text-2xl"></i>
                            <span class="font-medium">Employee</span>
                            <i class="fas fa-chevron-down profile-dropdown-arrow" aria-hidden="true"></i>
                        </button>
                        <div id="employeeProfileMenu" class="employee-profile-menu" role="menu" hidden>
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

            <main class="min-w-0 flex-1 overflow-y-auto px-4 py-6 md:px-6">
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-500 px-6 py-3 text-white animate-fade-in">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <div id="notificationToastContainer" class="notification-toast-container"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            if (toggle) {
                toggle.addEventListener('click', openSidebar);
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }

            const profileToggle = document.getElementById('employeeProfileToggle');
            const profileMenu = document.getElementById('employeeProfileMenu');

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

            const notificationToggle = document.getElementById('employeeNotificationToggle');
            const notificationPanel = document.getElementById('employeeNotificationPanel');
            const notificationList = document.getElementById('employeeNotificationList');
            const notificationBadge = document.getElementById('employeeNotificationBadge');
            const markAllReadButton = document.getElementById('employeeMarkAllRead');
            const toastContainer = document.getElementById('notificationToastContainer');
            const notificationUrl = '{{ route('employee.notifications.index') }}';
            const markReadUrl = '{{ route('employee.notifications.mark-read', ['id' => '__ID__']) }}';
            const markAllReadUrl = '{{ route('employee.notifications.mark-all-read') }}';
            let notificationsCache = [];
            const notificationSeenStorageKey = 'employee-notification-seen-ids';
            const notificationHighlightStorageKey = 'employee-notification-highlight-room-id';
            const notificationReservationStorageKey = 'employee-notification-highlight-reservation-id';
            const notificationReservationTypeStorageKey = 'employee-notification-highlight-reservation-type';
            const notificationReservationTabStorageKey = 'employee-notification-highlight-reservation-tab';
            const notificationMessageStorageKey = 'employee-notification-highlight-message-id';
            const seenNotificationIds = new Set();
            let notificationBaselineLoaded = false;

            try {
                const storedNotificationIds = JSON.parse(sessionStorage.getItem(notificationSeenStorageKey) || '[]');
                if (Array.isArray(storedNotificationIds)) {
                    storedNotificationIds.forEach(id => seenNotificationIds.add(String(id)));
                }
            } catch (error) {
                console.warn('Could not restore notification state:', error);
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

                    if (roomId) {
                        sessionStorage.setItem(notificationHighlightStorageKey, roomId);
                    }
                    if (reservationId) {
                        sessionStorage.setItem(notificationReservationStorageKey, reservationId);
                    }
                    if (reservationType) {
                        sessionStorage.setItem(notificationReservationTypeStorageKey, reservationType);
                    }
                    if (reservationTab) {
                        sessionStorage.setItem(notificationReservationTabStorageKey, reservationTab);
                    }
                    if (messageId) {
                        sessionStorage.setItem(notificationMessageStorageKey, messageId);
                    }
                } catch (error) {
                    console.warn('Could not queue employee notification target:', error);
                }
            }

            function formatRelativeTime(value) {
                if (!value) return 'just now';
                const date = new Date(value);
                const diffMs = Date.now() - date.getTime();
                const diffMinutes = Math.max(1, Math.round(diffMs / 60000));
                if (diffMinutes < 60) return diffMinutes + 'm ago';
                const diffHours = Math.round(diffMinutes / 60);
                if (diffHours < 24) return diffHours + 'h ago';
                const diffDays = Math.round(diffHours / 24);
                return diffDays + 'd ago';
            }

            async function openNotification(notificationId, targetUrl) {
                try {
                    const response = await fetch(markReadUrl.replace('__ID__', notificationId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) throw new Error(`Mark-read request returned ${response.status}`);
                    const result = await response.json();
                    updateBadge(result.unread_count ?? 0);
                    notificationList?.querySelectorAll('.notification-item').forEach((item) => {
                        if (item.dataset.id === String(notificationId)) item.classList.remove('unread');
                    });

                    if (targetUrl) {
                        persistNotificationTarget(targetUrl);
                        window.location.assign(targetUrl);
                    }
                } catch (error) {
                    console.error('Employee notification could not be marked read:', error);
                }
            }

            function renderNotificationList(items) {
                notificationsCache = items;
                if (!notificationList) return;
                if (!items.length) {
                    notificationList.innerHTML = '<div class="notification-empty">No notifications yet.</div>';
                    return;
                }

                notificationList.innerHTML = items.map(item => `
                    <button type="button" class="notification-item ${item.is_read ? '' : 'unread'}" data-id="${item.id}" data-url="${item.url || ''}">
                        <div class="flex items-start gap-2">
                            <i class="${item.icon || 'fas fa-bell'} mt-1 text-orange-500"></i>
                            <div class="min-w-0 flex-1">
                                <div class="title">${item.title}</div>
                                <div class="message">${(item.message || '').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                                <div class="meta">${formatRelativeTime(item.created_at)}</div>
                            </div>
                        </div>
                    </button>
                `).join('');

                notificationList.querySelectorAll('.notification-item').forEach(function (button) {
                    button.addEventListener('click', function () {
                        openNotification(button.dataset.id, button.dataset.url);
                    });
                });
            }

            function updateBadge(count) {
                if (!notificationBadge) return;
                if (count > 0) {
                    notificationBadge.textContent = String(count > 99 ? '99+' : count);
                    notificationBadge.classList.remove('hidden');
                    return;
                }
                notificationBadge.textContent = '0';
                notificationBadge.classList.add('hidden');
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

            function showToast(notification) {
                if (!toastContainer || !notification) return;
                const toast = document.createElement('div');
                toast.className = 'notification-toast';
                toast.innerHTML = `
                    <div class="notification-toast-header">
                        <div class="flex items-center gap-2 min-w-0">
                            <i class="${notification.icon || 'fas fa-bell'} text-orange-500"></i>
                            <div class="toast-title">${notification.title}</div>
                        </div>
                        <button type="button" class="toast-close" aria-label="Dismiss notification">×</button>
                    </div>
                    <div class="toast-message">${(notification.message || '').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                    <button type="button" class="toast-action">${notification.action_label || 'View'}</button>
                `;
                const closeButton = toast.querySelector('.toast-close');
                const actionButton = toast.querySelector('.toast-action');
                closeButton.addEventListener('click', () => toast.remove());
                actionButton.addEventListener('click', () => {
                    openNotification(notification.id, notification.url);
                    toast.remove();
                });
                toastContainer.appendChild(toast);
                setTimeout(() => toast.remove(), 6000);
            }

            function loadNotifications() {
                if (document.visibilityState !== 'visible') return;

                fetch(notificationUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                })
                    .then(response => response.ok ? response.json() : null)
                    .then(data => {
                        if (!data || !Array.isArray(data.data)) return;
                        const newItems = [];
                        data.data.forEach(item => {
                            const id = String(item.id);
                            const alreadySeen = seenNotificationIds.has(id);
                            const ageMs = Date.now() - Date.parse(item.created_at || '');
                            const recentlyCreated = Number.isFinite(ageMs) && ageMs >= 0 && ageMs <= 10 * 60 * 1000;
                            if (!alreadySeen && !item.is_read && (notificationBaselineLoaded || recentlyCreated)) {
                                newItems.push(item);
                            }
                            seenNotificationIds.add(id);
                        });
                        notificationBaselineLoaded = true;
                        try {
                            sessionStorage.setItem(notificationSeenStorageKey, JSON.stringify(Array.from(seenNotificationIds).slice(-100)));
                        } catch (error) {
                            console.warn('Could not persist notification state:', error);
                        }

                        updateBadge(data.unread_count ?? data.data.filter(item => !item.is_read).length);
                        updateSidebarNotificationBadges(data.module_counts);
                        renderNotificationList(data.data);
                        newItems.reverse().slice(0, 3).forEach(showToast);
                    })
                    .catch(error => console.error('Notification refresh failed:', error));
            }

            if (notificationToggle && notificationPanel) {
                const positionNotificationPanel = () => {
                    const headerBottom = notificationToggle.closest('header')?.getBoundingClientRect().bottom
                        ?? notificationToggle.getBoundingClientRect().bottom;
                    notificationPanel.style.setProperty('--notification-panel-top', `${headerBottom + 8}px`);
                };

                notificationToggle.addEventListener('click', function () {
                    const hidden = notificationPanel.hidden;
                    notificationPanel.hidden = !hidden;
                    notificationToggle.setAttribute('aria-expanded', String(!notificationPanel.hidden));
                    if (!notificationPanel.hidden) {
                        positionNotificationPanel();
                        loadNotifications();
                    }
                });
                window.addEventListener('resize', () => {
                    if (!notificationPanel.hidden) positionNotificationPanel();
                });
                document.addEventListener('click', function (event) {
                    if (!notificationToggle.contains(event.target) && !notificationPanel.contains(event.target)) {
                        notificationPanel.hidden = true;
                        notificationToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            if (markAllReadButton) {
                markAllReadButton.addEventListener('click', function () {
                    fetch(markAllReadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    }).then(() => loadNotifications());
                });
            }

            loadNotifications();
            window.setInterval(loadNotifications, 5000);
            document.addEventListener('visibilitychange', loadNotifications);
        });
    </script>
</body>
</html>
