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
                <div class="flex gap-4">
                    <input type="text" 
                           name="order_code" 
                           id="order_code"
                           placeholder="Masukkan nomor order (contoh: ORD0001)"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    <button type="button" 
                            id="fetchOrder" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Ambil Data Order
                    </button>
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
                           value="{{ old('supplier') }}" required>
                    @error('supplier')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                    <input type="text" name="invoice_number" id="invoice_number"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('invoice_number') }}">
                    @error('invoice_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Received Items</h3>
                    <button type="button" id="addItem" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Item
                    </button>
                </div>

                <div id="receivedItems">
                    <div class="received-item border rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                                <input type="text" name="items[0][name]" class="item-name w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Received</label>
                                <input type="number" name="items[0][quantity_received]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price</label>
                                <input type="number" name="items[0][unit_price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" step="0.01" min="0" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                                <select name="items[0][condition]" class="item-condition w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Condition</option>
                                    <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="partial">Partial</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-item bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('goods-received.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Record Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;

    // Add new item
    document.getElementById('addItem').addEventListener('click', function() {
        const receivedItems = document.getElementById('receivedItems');
        const newItem = document.createElement('div');
        newItem.className = 'received-item border rounded-lg p-4 mb-4';
        newItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                    <input type="text" name="items[${itemIndex}][name]" class="item-name w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Received</label>
                    <input type="number" name="items[${itemIndex}][quantity_received]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price</label>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" step="0.01" min="0" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                    <select name="items[${itemIndex}][condition]" class="item-condition w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select Condition</option>
                        <option value="good">Good</option>
                        <option value="damaged">Damaged</option>
                        <option value="partial">Partial</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove-item bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        receivedItems.appendChild(newItem);
        itemIndex++;
    });

    // Remove item
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const receivedItems = document.querySelectorAll('.received-item');
            if (receivedItems.length > 1) {
                e.target.closest('.received-item').remove();
            } else {
                alert('At least one item is required');
            }
        }
    });

    // Load PO details when selected
    document.getElementById('purchase_order_id').addEventListener('change', function() {
        const poId = this.value;
        if (poId) {
            fetch(`/api/goods-received/po-details?po_id=${poId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('supplier').value = data.data.supplier;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
});
</script>
@endsection
