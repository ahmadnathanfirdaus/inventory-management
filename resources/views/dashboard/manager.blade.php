<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Pending Approvals</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_approvals'] }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Approved Today</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['approved_today'] }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Total Approved</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_approved'] }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Total Rejected</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['total_rejected'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('approvals.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Lihat Pending Approvals
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Pending Orders</h3>
                    <div class="space-y-3">
                        @forelse ($pending_orders as $order)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $order->order_number }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Dibuat oleh: {{ $order->creator->name }} • {{ $order->created_at->format('d M Y H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ Str::limit($order->description, 100) }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ $order->items->count() }} item(s)
                                            </p>
                                            <p class="text-lg font-bold text-green-600">
                                                Rp {{ number_format($order->getTotalAmount(), 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('approvals.show', $order) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            Review
                                        </a>
                                        <form action="{{ route('approvals.approve', $order) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
                                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui order ini?')">
                                                Setujui
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada order yang perlu disetujui</p>
                        @endforelse
                    </div>

                    @if ($pending_orders->hasPages())
                        <div class="mt-4">
                            {{ $pending_orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
