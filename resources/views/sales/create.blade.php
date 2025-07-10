@extends('layouts.sidebar')

@section('title', 'Create New Sale')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Create New Sale</h1>
            <a href="{{ route('sales.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Sales
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

        <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
            @csrf

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Sale Items</h3>
                    <button type="button" id="addItem" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Item
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
                            <tr class="item-row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" class="product-code w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly placeholder="Product Code">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select name="items[0][product_id]" class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
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
                                    <input type="number" name="items[0][price]" class="item-price w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" step="1000" min="0" readonly>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="items[0][quantity]" class="item-quantity w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" required>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="items[0][total]" class="item-total w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button type="button" class="remove-item text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="text-xl font-semibold text-gray-900">
                    Total: Rp <span id="grandTotal">0</span>
                </div>
                <div class="space-x-3">
                    <a href="{{ route('sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create Sale
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = 1;

document.addEventListener('DOMContentLoaded', function() {
    updateCalculations();

    document.getElementById('addItem').addEventListener('click', function() {
        addItemRow();
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            updateProductPrice(e.target);
        }
        if (e.target.classList.contains('item-quantity')) {
            updateItemTotal(e.target);
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            removeItemRow(e.target.closest('tr'));
        }
    });
});

function addItemRow() {
    const tbody = document.getElementById('itemsTable');
    const newRow = tbody.querySelector('.item-row').cloneNode(true);

    // Update name attributes
    newRow.querySelectorAll('input, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace('[0]', `[${itemIndex}]`));
        }
        input.value = '';
    });

    // Reset product code field specifically
    const codeInput = newRow.querySelector('.product-code');
    if (codeInput) {
        codeInput.value = '';
    }

    tbody.appendChild(newRow);
    itemIndex++;
}

function removeItemRow(row) {
    if (document.querySelectorAll('.item-row').length > 1) {
        row.remove();
        updateCalculations();
    }
}

function updateProductPrice(select) {
    const option = select.options[select.selectedIndex];
    const row = select.closest('tr');
    const priceInput = row.querySelector('.item-price');
    const codeInput = row.querySelector('.product-code');

    if (option.value) {
        priceInput.value = option.getAttribute('data-price');
        codeInput.value = option.getAttribute('data-code');
        updateItemTotal(row.querySelector('.item-quantity'));
    } else {
        priceInput.value = '';
        codeInput.value = '';
        updateItemTotal(row.querySelector('.item-quantity'));
    }
}

function updateItemTotal(quantityInput) {
    const row = quantityInput.closest('tr');
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const quantity = parseFloat(quantityInput.value) || 0;
    const total = price * quantity;

    row.querySelector('.item-total').value = total;
    updateCalculations();
}

function updateCalculations() {
    let grandTotal = 0;

    document.querySelectorAll('.item-total').forEach(input => {
        grandTotal += parseFloat(input.value) || 0;
    });

    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString('id-ID');
}
</script>
@endsection
