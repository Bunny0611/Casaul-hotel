﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CASAUL Hotel - Housekeeping</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Poppins', sans-serif; }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            width: 100%;
            background: #f3f4f6;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
        }

        header {
            margin: 0;
            padding-top: 0;
        }

        .sidebar {
    background-color: #800000 !important;
    background-image: none !important;
}
.sidebar nav, .sidebar .mt-auto, .sidebar .p-6 {
    background: transparent !important;
}
        
.header {
    background: linear-gradient(90deg, #ff6b35 0%, #ff8c42 100%);
}

 .nav-item {
    display: flex !important;
    width: 100%;
    justify-content: flex-start;
    align-items: center;
    margin: 0.2rem 0;
    border-radius: 0.9rem;
    transition: all 0.25s ease;
    color: rgba(255,255,255,0.9);
}

.nav-item:hover {
    background: rgba(255,255,255,0.14);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
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
        
        .clean { background: #10b981; }
        .dirty { background: #ef4444; }
        .in-progress { background: #f59e0b; }
        
        .status-available { background: #10b981; }
        .status-occupied { background: #ef4444; }
        .status-maintenance { background: #f59e0b; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 text-white overflow-y-auto transform -translate-x-full transition-transform duration-300 md:translate-x-0 md:flex-shrink-0 md:overflow-y-auto md:fixed md:inset-y-0 md:left-0 flex flex-col">
            <div class="p-6">
                
                <h1 class="text-2xl font-bold tracking-wider">
                    <i class="fas fa-hotel mr-2"></i>CASAUL</h1>
                <p class="text-sm text-gray-300 mt-1">Housekeeping Portal</p>
            </div>
            
            <nav class="mt-20 flex flex-col gap-2 px-3">
                <a href="{{ route('housekeeping.dashboard') }}" class="nav-item w-full flex items-center px-5 py-3.5 transition-all duration-300 {{ request()->routeIs('housekeeping.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-broom w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('housekeeping.assigned-rooms') }}" class="nav-item w-full flex items-center px-5 py-3.5 transition-all duration-300 {{ request()->routeIs('housekeeping.assigned-rooms') ? 'active' : '' }}">
                    <i class="fas fa-bed w-6"></i>
                    <span>Assigned Rooms</span>
                </a>
                <a href="{{ route('housekeeping.room-status-update') }}" class="nav-item w-full flex items-center px-5 py-3.5 transition-all duration-300 {{ request()->routeIs('housekeeping.room-status-update') ? 'active' : '' }}">
                    <i class="fas fa-sync-alt w-6"></i>
                    <span>Room Status Update</span>
                </a>
                <a href="{{ route('housekeeping.guest-requests') }}" class="nav-item w-full flex items-center px-5 py-3.5 transition-all duration-300 {{ request()->routeIs('housekeeping.guest-requests') ? 'active' : '' }}">
                    <i class="fas fa-bell w-6"></i>
                    <span>Guest Requests</span>
                </a>
                <a href="{{ route('housekeeping.maintenance-report') }}" class="nav-item w-full flex items-center px-5 py-3.5 transition-all duration-300 {{ request()->routeIs('housekeeping.maintenance-report') ? 'active' : '' }}">
                    <i class="fas fa-tools w-6"></i>
                    <span>Maintenance Report</span>
                </a>
<<<<<<< HEAD
                <a href="{{ route('housekeeping.cleaning-history') }}" class="nav-item w-full flex items-center px-5 py-3.5 transition-all duration-300 {{ request()->routeIs('housekeeping.cleaning-history') ? 'active' : '' }}">
                    <i class="fas fa-history w-6"></i>
                    <span>Cleaning History</span>
                </a>
=======
>>>>>>> 567910577cfeffed3b9fcc998ab381d3b8efecfd
            </nav>
            
            <div class="mt-auto pt-8 px-6 pb-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-6 py-3 text-gray-300 hover:text-white transition-colors bg-transparent border-none cursor-pointer">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Sidebar backdrop -->
        <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64 min-h-screen">
            <!-- Header -->
            <header class="header sticky top-0 z-50 text-white px-6 py-3 flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="md:hidden p-1.5 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-semibold mt-0">Welcome,  {{ Auth::user()?->name ?? 'Housekeeper' }}!</h2>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 w-64">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>
                    </div>
                    <div class="flex items-center space-x-2 bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <span class="font-medium"> {{ Auth::user()?->name ?? 'Housekeeper' }}</span>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto px-6 pb-6 pt-3">
                @if(session('success'))
                    <div class="bg-green-500 text-white px-6 py-3 rounded-lg mb-6 animate-fade-in">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>

