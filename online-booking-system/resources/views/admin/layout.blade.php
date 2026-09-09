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
.sidebar nav,
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

        @media (max-width: 767px) {
            .sidebar {
                width: min(80vw, 18rem);
            }

            .header-search {
                width: 100%;
            }

            .main-content-panel {
                padding: 1rem;
            }
        }

        html, body { max-width: 100%; overflow-x: hidden; }
        .main-content-panel { min-width: 0; overflow-x: hidden; }
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
        .status-reserved { background: #04d9ff; }
        
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
            
            <nav class="mt-6 flex flex-col px-3">
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
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden md:ml-64">
            <!-- Header -->
            <header class="header text-white px-4 py-4 sm:px-6 shadow-lg">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <button id="sidebarToggle" class="md:hidden p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Welcome, {{ auth()->user()->name }}!</h2>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:space-x-4">
                        <div class="relative header-search w-full sm:w-64">
                            <input id="adminHeaderSearch" type="search" placeholder="Search..." aria-label="Search this page" autocomplete="off" class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 w-full">
                            <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>
                        </div>
                        <div class="flex items-center justify-center space-x-2 bg-white/20 px-3 py-2 rounded-lg text-sm sm:text-base">
                            <i class="fas fa-user-circle text-xl sm:text-2xl"></i>
                            <span class="font-medium truncate max-w-[8rem] sm:max-w-none">{{ auth()->user()->name }}</span>
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
