@extends('layouts.sidebar')

@section('title', 'Edit Sale')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <div>
                                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                    Dashboard
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <a href="{{ route('sales.index') }}" class="ml-4 text-gray-400 hover:text-gray-500">
                                    Sales
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <span class="ml-4 text-gray-500">Edit {{ $sale->transaction_code }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Edit Sale #{{ $sale->transaction_code }}
                </h2>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('sales.show', $sale) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Sale
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
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

                <form action="{{ route('sales.update', $sale) }}" method="POST" id="saleForm">
                    @csrf
                    @method('PUT')

                    <!-- Sale Info -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Transaction Code</label>
                                <input type="text" value="{{ $sale->transaction_code }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cashier</label>
                                <input type="text" value="{{ $sale->cashier->name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="text" value="{{ $sale->purchase_date->format('d/m/Y H:i') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Items -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Sale Items</h3>
                            <button type="button" id="addItem" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable" class="bg-white divide-y divide-gray-200">
                                    @foreach($sale->items as $index => $item)
                                    <tr class="item-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="text" class="product-code w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly value="{{ $item->product->product_code }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select name="items[{{ $index }}][product_id]" class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->product_code }}"
                                                            data-code="{{ $product->product_code }}"
                                                            data-price="{{ $product->product_price }}"
                                                            data-stock="{{ $product->stock_quantity }}"
                                                            {{ $product->product_code == $item->product->product_code ? 'selected' : '' }}>
                                                        {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" name="items[{{ $index }}][price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" step="1000" min="0" value="{{ $item->unit_price }}" readonly>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" name="items[{{ $index }}][quantity]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" value="{{ $item->quantity }}" required>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" name="items[{{ $index }}][total]" class="item-total w-full rounded-md border-gray-300 shadow-sm bg-gray-50" value="{{ $item->sub_total }}" readonly>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button type="button" class="remove-item text-red-600 hover:text-red-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-6">
                        <div class="flex justify-between items-center">
                            <div class="text-lg font-medium text-gray-900">
                                Total Amount:
                                <span id="grandTotal" class="text-2xl font-bold text-green-600">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Sale
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $sale->items->count() }};

    // Add item functionality
    document.getElementById('addItem').addEventListener('click', function() {
        const tableBody = document.getElementById('itemsTable');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';

        newRow.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" class="product-code w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly placeholder="Product Code">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <select name="items[${itemIndex}][product_id]" class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->product_code }}"
                                data-code="{{ $product->product_code }}"
                                data-price="{{ $product->product_price }}"
                                data-stock="{{ $product->stock_quantity }}">
                            {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[${itemIndex}][price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" step="1000" min="0" readonly>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[${itemIndex}][quantity]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" required>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[${itemIndex}][total]" class="item-total w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <button type="button" class="remove-item text-red-600 hover:text-red-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;

        tableBody.appendChild(newRow);
        itemIndex++;

        // Add event listeners to new row
        addRowEventListeners(newRow);
    });

    // Function to add event listeners to a row
    function addRowEventListeners(row) {
        const productSelect = row.querySelector('.product-select');
        const priceInput = row.querySelector('.item-price');
        const quantityInput = row.querySelector('.item-quantity');
        const totalInput = row.querySelector('.item-total');
        const codeInput = row.querySelector('.product-code');
        const removeButton = row.querySelector('.remove-item');

        // Product selection
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                codeInput.value = selectedOption.dataset.code;
                priceInput.value = selectedOption.dataset.price;
                calculateRowTotal();
            } else {
                codeInput.value = '';
                priceInput.value = '';
                totalInput.value = '';
            }
        });

        // Quantity change
        quantityInput.addEventListener('input', calculateRowTotal);

        // Remove item
        removeButton.addEventListener('click', function() {
            row.remove();
            updateGrandTotal();
        });

        function calculateRowTotal() {
            const price = parseFloat(priceInput.value) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            const total = price * quantity;
            totalInput.value = total;
            updateGrandTotal();
        }
    }

    // Add event listeners to existing rows
    document.querySelectorAll('.item-row').forEach(addRowEventListeners);

    // Update grand total
    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-total').forEach(function(input) {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }
});
</script>
@endsection
