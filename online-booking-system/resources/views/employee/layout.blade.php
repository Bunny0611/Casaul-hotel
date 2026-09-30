<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            grid-template-columns: minmax(0, 1fr) auto auto;
            align-items: center;
            gap: 0.75rem 1rem;
            width: 100%;
        }

        .employee-header-title { grid-column: 1; min-width: 0; }
        .employee-header-search { grid-column: 2; width: 16rem; max-width: 16rem; }
        .employee-header-profile { grid-column: 3; }
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

            .employee-header-layout { grid-template-columns: minmax(0, 1fr) auto; }
            .employee-header-title { grid-column: 1; }
            .employee-header-search {
                grid-column: 1 / -1;
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
            .employee-header-profile { grid-column: 2; grid-row: 1; }
            .header-left h2 { font-size: 0.95rem; }

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
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 text-white overflow-y-auto transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0 md:flex-shrink-0 md:overflow-y-auto md:static flex flex-col">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider">CASAUL</h1>
                <p class="text-sm text-gray-300 mt-1">Employee Portal</p>
            </div>

            <nav class="mt-10 flex flex-col px-3">
                <a href="{{ route('employee.dashboard') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('employee.reservation') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.reservation') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check w-6"></i>
                    <span>Reservation</span>
                </a>
                <a href="{{ route('employee.calendar') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.calendar') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span>Calendar</span>
                </a>
                <a href="{{ route('employee.refunds') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.refunds') ? 'active' : '' }}">
                    <i class="fas fa-rotate-left w-6"></i>
                    <span>Refund History</span>
                </a>
                <a href="{{ route('employee.checkin') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.checkin') ? 'active' : '' }}">
                    <i class="fas fa-sign-in-alt w-6"></i>
                    <span>Check-in/Check-out</span>
                </a>
                <a href="{{ route('employee.room-status') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.room-status') ? 'active' : '' }}">
                    <i class="fas fa-bed w-6"></i>
                    <span>Room Status</span>
                </a>
                <a href="{{ route('employee.guest-requests') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.guest-requests') ? 'active' : '' }}">
                    <i class="fas fa-hotel w-6"></i>
                    <span>Guest Requests</span>
                </a>
                <a href="{{ route('employee.messages') }}" class="nav-item px-5 py-3.5 {{ request()->routeIs('employee.messages') ? 'active' : '' }}">
                    <i class="fas fa-comments w-6"></i>
                    <span>Messages</span>
                </a>
            </nav>

        </aside>

        <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

        <div class="flex-1 flex flex-col overflow-hidden md:ml-0">
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

            <main class="flex-1 overflow-y-auto px-4 py-6 md:px-6">
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

        });
    </script>
</body>
</html>
