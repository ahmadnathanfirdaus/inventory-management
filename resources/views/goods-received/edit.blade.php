@extends('layouts.sidebar')

@section('title', 'Edit Goods Received')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Goods Received #{{ $goodsReceived->id }}</h1>
            <a href="{{ route('goods-received.show', $goodsReceived) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Details
            </a>
        </div>
    </div>

    <div class="p-6">
        <form action="{{ route('goods-received.update', $goodsReceived) }}" method="POST" id="goodsForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="purchase_order_id" class="block text-sm font-medium text-gray-700 mb-2">Purchase Order</label>
                    <select name="purchase_order_id" id="purchase_order_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                        <option value="">Select Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" {{ old('purchase_order_id', $goodsReceived->purchase_order_id) == $po->id ? 'selected' : '' }}>
                                #{{ $po->id }} - {{ $po->supplier }} ({{ $po->order_date ? $po->order_date->format('M d, Y') : 'No Date' }})
                            </option>
                        @endforeach
                    </select>
                    @error('purchase_order_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Received Date</label>
                    <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-300">
                        {{ $goodsReceived->received_at ? $goodsReceived->received_at->format('M d, Y') : 'Not Set' }} (Read Only)
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <input type="text" name="supplier" id="supplier"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('supplier', $goodsReceived->supplier) }}" required>
                    @error('supplier')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                    <input type="text" name="invoice_number" id="invoice_number"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('invoice_number', $goodsReceived->invoice_number) }}">
                    @error('invoice_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $goodsReceived->notes) }}</textarea>
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
                    @foreach($goodsReceived->items as $index => $item)
                    <div class="received-item border rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                                <input type="text" name="items[{{ $index }}][name]" class="item-name w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       value="{{ old('items.'.$index.'.name', $item->name) }}" required>
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Received</label>
                                <input type="number" name="items[{{ $index }}][quantity_received]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       value="{{ old('items.'.$index.'.quantity_received', $item->quantity_received) }}" min="1" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price</label>
                                <input type="number" name="items[{{ $index }}][unit_price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       value="{{ old('items.'.$index.'.unit_price', $item->unit_price) }}" step="0.01" min="0" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                                <select name="items[{{ $index }}][condition]" class="item-condition w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Condition</option>
                                    <option value="good" {{ old('items.'.$index.'.condition', $item->condition) === 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="damaged" {{ old('items.'.$index.'.condition', $item->condition) === 'damaged' ? 'selected' : '' }}>Damaged</option>
                                    <option value="partial" {{ old('items.'.$index.'.condition', $item->condition) === 'partial' ? 'selected' : '' }}>Partial</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-item bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('goods-received.show', $goodsReceived) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Update Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $goodsReceived->items->count() }};

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
});
</script>
@endsection
