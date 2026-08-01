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
        }

        .header {
            background: linear-gradient(90deg, #ff6b35 0%, #ff8c42 100%);
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

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-available { background: #10b981; }
        .status-occupied { background: #ef4444; }
        .status-maintenance { background: #f59e0b; }
        .status-pending { background: #f59e0b; }
        .status-confirmed { background: #10b981; }
        .status-cancelled { background: #ef4444; }
        .status-completed { background: #3b82f6; }
        .status-cleaning { background: #8b5cf6; }
        .status-dirty { background: #64748b; }
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

            <div class="mt-auto px-6 pb-6 pt-8">
                <a href="{{ route('home') }}" class="flex items-center px-5 py-3 text-gray-300 transition-colors hover:text-white">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

        <div class="flex-1 flex flex-col overflow-hidden md:ml-0">
            <header class="header text-white px-6 py-4 flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="md:hidden rounded-lg bg-white/20 p-2 transition-colors hover:bg-white/30">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-semibold">@yield('pageTitle', 'Welcome to CASAUL Hotel Management')</h2>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="w-64 rounded-lg bg-white/20 px-4 py-2 text-white placeholder-gray-200 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>
                    </div>
                    <div class="flex items-center space-x-2 rounded-lg bg-white/20 px-4 py-2">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <span class="font-medium">Employee</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
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
        });
    </script>
</body>
</html>
