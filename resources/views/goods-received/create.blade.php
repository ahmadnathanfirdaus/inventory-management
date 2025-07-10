@extends('layouts.sidebar')

@section('title', 'Record Goods Received')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Record Goods Received</h1>
            <a href="{{ route('goods-received.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="p-6">
        <form action="{{ route('goods-received.store') }}" method="POST" id="goodsForm">
            @csrf

            <!-- Order Code Input -->
            <div class="mb-6">
                <label for="order_code" class="block text-sm font-medium text-gray-700 mb-2">Nomor Order *</label>

                <!-- Available Orders Dropdown -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atau pilih dari daftar order yang tersedia:</label>
                    <select id="availableOrders" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Memuat daftar order... --</option>
                    </select>
                </div>

                <!-- Manual Input -->
                <div class="flex gap-4">
                    <input type="text"
                           name="order_code"
                           id="order_code"
                           placeholder="Atau masukkan nomor order manual (contoh: ORD0001)"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    <button type="button"
                            id="fetchOrder"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Ambil Data Order
                    </button>
                </div>

                <!-- Error Alert -->
                <div id="orderError" class="hidden mt-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="orderErrorMessage"></span>
                    </div>
                </div>

                @error('order_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Order Details (Hidden initially) -->
            <div id="orderDetails" class="hidden">
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Order</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Order Code:</span>
                            <span id="displayOrderCode" class="text-gray-900"></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Status:</span>
                            <span id="displayStatus" class="text-gray-900"></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Admin:</span>
                            <span id="displayAdmin" class="text-gray-900"></span>
                        </div>
                        <div class="col-span-3">
                            <span class="font-medium text-gray-700">Deskripsi:</span>
                            <span id="displayDescription" class="text-gray-900"></span>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Item yang Diterima</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Diterima</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Items will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('goods-received.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium">
                        Record Goods Received
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fetchOrderBtn = document.getElementById('fetchOrder');
    const orderCodeInput = document.getElementById('order_code');
    const availableOrdersSelect = document.getElementById('availableOrders');
    const orderDetails = document.getElementById('orderDetails');
    const orderError = document.getElementById('orderError');
    const orderErrorMessage = document.getElementById('orderErrorMessage');

    // Load available orders on page load
    loadAvailableOrders();

    // Handle available orders dropdown selection
    availableOrdersSelect.addEventListener('change', function() {
        if (this.value) {
            orderCodeInput.value = this.value;
            hideError();
        }
    });

    // Load available orders
    function loadAvailableOrders() {
        fetch('/api/available-orders', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                availableOrdersSelect.innerHTML = '<option value="">-- Pilih Order --</option>';
                data.orders.forEach(order => {
                    const option = document.createElement('option');
                    option.value = order.order_code;
                    option.textContent = `${order.order_code} - ${order.admin} (${order.order_date}) - PO: ${order.po_number}`;
                    availableOrdersSelect.appendChild(option);
                });
            } else {
                availableOrdersSelect.innerHTML = '<option value="">-- Tidak ada order tersedia --</option>';
            }
        })
        .catch(error => {
            console.error('Error loading available orders:', error);
            availableOrdersSelect.innerHTML = '<option value="">-- Error memuat data --</option>';
        });
    }

    // Show error message
    function showError(message) {
        orderErrorMessage.textContent = message;
        orderError.classList.remove('hidden');
        orderDetails.classList.add('hidden');
    }

    // Hide error message
    function hideError() {
        orderError.classList.add('hidden');
    }

    fetchOrderBtn.addEventListener('click', function() {
        const orderCode = orderCodeInput.value.trim();

        if (!orderCode) {
            showError('Masukkan nomor order terlebih dahulu');
            return;
        }

        hideError();

        // Show loading state
        fetchOrderBtn.textContent = 'Loading...';
        fetchOrderBtn.disabled = true;

        // Fetch order details
        fetch(`{{ route('goods-received.get-order-details') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ order_code: orderCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showError(data.error);
                return;
            }

            // Populate order details
            document.getElementById('displayOrderCode').textContent = data.order.order_code;
            document.getElementById('displayStatus').textContent = data.order.status;
            document.getElementById('displayAdmin').textContent = data.order.admin?.name || 'N/A';
            document.getElementById('displayDescription').textContent = data.order.notes || 'No description';

            // Populate items table
            const tbody = document.getElementById('itemsTableBody');
            tbody.innerHTML = '';

            data.items.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${item.item_name || item.product?.product_name || 'Unknown Product'}
                        <input type="hidden" name="items[${index}][product_code]" value="${item.product_code}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${item.quantity || item.order_quantity || 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="number"
                               name="items[${index}][quantity_received]"
                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               min="0"
                               max="${item.quantity || item.order_quantity}"
                               value="${item.quantity || item.order_quantity}"
                               required>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <select name="items[${index}][status]"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="complete">Complete</option>
                            <option value="partial">Partial</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Show order details section
            orderDetails.classList.remove('hidden');
        })
        .catch(error => {
            showError('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            fetchOrderBtn.textContent = 'Ambil Data Order';
            fetchOrderBtn.disabled = false;
        });
    });
});
</script>
@endsection
