<div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out"
     x-data="{ open: true }"
     :class="{ '-translate-x-full lg:translate-x-0': !open, 'translate-x-0': open }"
     id="sidebar">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-4 bg-gray-800">
        <div class="flex items-center">
            <h1 class="text-xl font-bold text-white">Inventory System</h1>
        </div>
        <button @click="open = !open" class="lg:hidden text-gray-300 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-6">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
            </svg>
            Dashboard
        </a>

        @if($user->isAdmin())
            <!-- Stock Barang Section for Admin -->
            <div class="mt-4">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Stock Management
                </div>
                <a href="{{ route('products.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('products.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Stock Barang
                    @php
                        $lowStockCount = \App\Models\Product::lowStock()->count();
                    @endphp
                    @if($lowStockCount > 0)
                        <span class="ml-auto bg-yellow-600 text-white text-xs px-2 py-1 rounded-full">{{ $lowStockCount }}</span>
                    @endif
                </a>
                <a href="{{ route('products.create') }}"
                   class="flex items-center px-4 py-2 ml-8 text-sm text-gray-400 hover:bg-gray-700 hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Stock
                </a>
            </div>

            <!-- Orders Section for Admin -->
            <div class="mt-4">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Order Management
                </div>
                <a href="{{ route('orders.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('orders.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Orders
                </a>
                <a href="{{ route('orders.create') }}"
                   class="flex items-center px-4 py-2 ml-8 text-sm text-gray-400 hover:bg-gray-700 hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Order Baru
                </a>
            </div>

            <!-- Goods Received Section for Admin -->
            <div class="mt-4">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Goods Management
                </div>
                <a href="{{ route('goods-received.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('goods-received.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Goods Received
                </a>
                <a href="{{ route('goods-received.create') }}"
                   class="flex items-center px-4 py-2 ml-8 text-sm text-gray-400 hover:bg-gray-700 hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Input Barang Baru
                </a>
            </div>

            <!-- Brand Management Section for Admin -->
            <div class="mt-4">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Brand & Distributor
                </div>
                <a href="{{ route('brands.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('brands.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Brands
                </a>
                <a href="{{ route('distributors.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('distributors.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                    Distributors
                </a>
            </div>
        @endif

        @if($user->isAdmin() || $user->isManager())
            <!-- Employee Management Section -->
            <div class="mt-4">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Employee Management
                </div>
                <a href="{{ route('employees.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('employees.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Employees
                </a>
                <a href="{{ route('employees.create') }}"
                   class="flex items-center px-4 py-2 ml-8 text-sm text-gray-400 hover:bg-gray-700 hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Employee
                </a>
            </div>

            <!-- Reports Section -->
            <div class="mt-4">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Reports
                </div>
                <a href="{{ route('reports.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('reports.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Reports
                </a>
            </div>
        @endif

        @if($user->isManager())
            <!-- Approvals Section for Manager -->
            <div class="mt-4">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Approval Management
                </div>
                <a href="{{ route('approvals.index') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('approvals.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pending Approvals
                    @php
                        $pendingCount = \App\Models\OrderRequest::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-red-600 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
            </div>
        @endif

        <!-- Point of Sale Section (All roles can access) -->
        <div class="mt-4">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                Point of Sale
            </div>
            <a href="{{ route('pos.index') }}"
               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('pos.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                </svg>
                POS System
            </a>
            <a href="{{ route('sales.index') }}"
               class="flex items-center px-4 py-2 ml-8 text-sm text-gray-400 hover:bg-gray-700 hover:text-white transition-colors {{ request()->routeIs('sales.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Sales History
            </a>
        </div>
    </nav>

    <!-- User Info -->
    <div class="absolute bottom-0 w-full p-4 bg-gray-800">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ $user->role }}</p>
            </div>
            <div class="ml-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mobile overlay -->
<div class="fixed inset-0 bg-gray-600 opacity-75 z-40 lg:hidden"
     x-show="open"
     @click="open = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"></div>
