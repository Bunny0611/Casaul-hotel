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
}
.sidebar nav,
.sidebar .mt-auto,
.sidebar .p-6 {
    background: transparent !important;
}
        
        .header {
            background: linear-gradient(90deg, #ff6b35 0%, #ff8c42 100%);
        }
        
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
        
        .status-available { background: #10b981; }
        .status-occupied { background: #ef4444; }
        .status-maintenance { background: #f59e0b; }
        .status-reserved { background: #04d9ff; }
        
        .status-pending { background: #f59e0b; }
        .status-confirmed { background: #10b981; }
        .status-cancelled { background: #ef4444; }
        .status-completed { background: #3b82f6; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 text-white overflow-y-auto transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0 md:flex-shrink-0 md:overflow-y-auto md:static flex flex-col">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider">CASAUL</h1>
                <p class="text-sm text-gray-300 mt-1">Hotel Management</p>
            </div>
            
            <nav class="mt-20 flex flex-col px-3">
                <a href="{{ route('admin.dashboard') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin') || request()->is('admin/') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reservations') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/reservations') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check w-6"></i>
                    <span>Reservations</span>
                </a>
                <a href="{{ route('admin.rooms') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/rooms') ? 'active' : '' }}">
                    <i class="fas fa-bed w-6"></i>
                    <span>Rooms</span>
                </a>
                <a href="{{ route('admin.manage-account') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/manage-account') ? 'active' : '' }}">
                    <i class="fas fa-user-cog w-6"></i>
                    <span>Manage Account</span>
                </a>
                <a href="{{ route('admin.messages') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/messages') ? 'active' : '' }}">
                    <i class="fas fa-comments w-6"></i>
                    <span>Messages</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar w-6"></i>
                    <span>Reports</span>
                </a>
                <a href="{{ route('admin.notifications') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell w-6"></i>
                    <span>Notifications</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-item w-full flex items-center px-3 py-2.5 transition-all duration-300 {{ request()->is('admin/settings') ? 'active' : '' }}">
                    <i class="fas fa-cog w-6"></i>
                    <span>Settings</span>
                </a>
            
            
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
        <div class="flex-1 flex flex-col overflow-hidden md:ml-0">
            <!-- Header -->
            <header class="header text-white px-6 py-4 flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="md:hidden p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-semibold">Welcome, {{ auth()->user()->name }}!</h2>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 w-64">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>
                    </div>
                    <div class="flex items-center space-x-2 bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <span class="font-medium">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
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
        });
    </script>
</body>
</html>
