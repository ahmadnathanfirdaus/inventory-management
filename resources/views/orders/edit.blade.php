@extends('layouts.sidebar')

@section('title', 'Edit Order')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Order #{{ $order->order_code }}</h1>
            <a href="{{ route('orders.show', $order) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Order
            </a>
        </div>
    </div>

    <div class="p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
            @csrf
            @method('PUT')

            <!-- Order Code -->
            <div class="mb-6">
                <label for="order_code" class="block text-sm font-medium text-gray-700 mb-2">Order Code</label>
                <input type="text"
                       name="order_code"
                       id="order_code"
                       value="{{ $order->order_code }}"
                       readonly
                       disabled
                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm text-gray-500 cursor-not-allowed">
                <p class="mt-1 text-xs text-gray-500">
                    Auto-generated code (cannot be changed)
                </p>
            </div>

            <!-- Order Date -->
            <div class="mb-6">
                <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Order Date</label>
                <input type="date"
                       name="order_date"
                       id="order_date"
                       value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                @error('order_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Order</label>
                <textarea name="description" id="description" rows="3" placeholder="Jelaskan tujuan atau keperluan order ini..."
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('description', $order->notes) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Order Items -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                    <button type="button" id="addItem" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Item
                    </button>
                </div>

                <div id="orderItems">
                    @forelse($order->orderRequestItems ?? [] as $index => $item)
                    <div class="order-item border rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <!-- Product Dropdown -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Produk *</label>
                                <select name="items[{{ $index }}][product_code]" class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->product_code }}"
                                                data-price="{{ $product->product_price }}"
                                                data-brand="{{ $product->brand->brand_name ?? '' }}"
                                                data-distributor="{{ $product->distributor->distributor_name ?? '' }}"
                                                {{ $item->product_code == $product->product_code ? 'selected' : '' }}>
                                            {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                @error('items.'.$index.'.product_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Distributor Dropdown -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Distributor *</label>
                                <select name="items[{{ $index }}][distributor_code]" class="distributor-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Pilih Distributor</option>
                                    @foreach($distributors as $distributor)
                                        <option value="{{ $distributor->distributor_code }}"
                                                {{ $item->distributor_code == $distributor->distributor_code ? 'selected' : '' }}>
                                            {{ $distributor->distributor_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('items.'.$index.'.distributor_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah *</label>
                                <input type="number" name="items[{{ $index }}][quantity]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       value="{{ old('items.'.$index.'.quantity', $item->order_quantity) }}" min="1" required>
                                @error('items.'.$index.'.quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan *</label>
                                <input type="number" name="items[{{ $index }}][unit_price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       value="{{ old('items.'.$index.'.unit_price', $item->estimated_price / $item->order_quantity) }}" step="1000" min="0" required>
                                @error('items.'.$index.'.unit_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remove Button -->
                            <div class="flex items-end">
                                <button type="button" class="remove-item bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">No items found. Click "Add Item" to create a new item.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('orders.show', $order) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ ($order->orderRequestItems ?? collect())->count() }};

    // Add new item
    document.getElementById('addItem').addEventListener('click', function() {
        const orderItems = document.getElementById('orderItems');
        const newItem = document.createElement('div');
        newItem.className = 'order-item border rounded-lg p-4 mb-4';
        newItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Product Dropdown -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produk *</label>
                    <select name="items[${itemIndex}][product_code]" class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->product_code }}"
                                    data-price="{{ $product->product_price }}"
                                    data-brand="{{ $product->brand->brand_name ?? '' }}"
                                    data-distributor="{{ $product->distributor->distributor_name ?? '' }}">
                                {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Distributor Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distributor *</label>
                    <select name="items[${itemIndex}][distributor_code]" class="distributor-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih Distributor</option>
                        @foreach($distributors as $distributor)
                            <option value="{{ $distributor->distributor_code }}">
                                {{ $distributor->distributor_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah *</label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" required>
                </div>

                <!-- Unit Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan *</label>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" step="1000" min="0" required>
                </div>

                <!-- Remove Button -->
                <div class="flex items-end">
                    <button type="button" class="remove-item bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        orderItems.appendChild(newItem);
        itemIndex++;
    });

    // Remove item
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const orderItems = document.querySelectorAll('.order-item');
            if (orderItems.length > 1) {
                e.target.closest('.order-item').remove();
            } else {
                alert('Minimal harus ada 1 item dalam order');
            }
        }
    });

    // Auto-fill price when product is selected
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const priceInput = e.target.closest('.order-item').querySelector('.item-price');
            const distributorSelect = e.target.closest('.order-item').querySelector('.distributor-select');

            if (selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                const distributor = selectedOption.getAttribute('data-distributor');

                if (price) {
                    priceInput.value = price;
                }

                // Auto-select distributor if available
                if (distributor) {
                    const distributorOptions = distributorSelect.options;
                    for (let i = 0; i < distributorOptions.length; i++) {
                        if (distributorOptions[i].text.includes(distributor)) {
                            distributorSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            } else {
                priceInput.value = '';
            }
        }
    });
});
</script>
@endsection
