@extends('layouts.sidebar')

@section('title', 'Add Product')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            <span class="sr-only">Home</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('products.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Products</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-500">Add Product</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Add Product</h1>
            <p class="mt-2 text-sm text-gray-600">Create a new product in your inventory</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white shadow rounded-lg">
                <!-- Basic Information -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Product Code -->
                        <div>
                            <label for="product_code" class="block text-sm font-medium text-gray-700">Product Code</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="product_code"
                                       id="product_code"
                                       value="{{ $nextCode }}"
                                       readonly
                                       disabled
                                       class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Auto-generated code
                            </p>
                        </div>

                        <!-- Product Name -->
                        <div>
                            <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="product_name"
                                       id="product_name"
                                       value="{{ old('product_name') }}"
                                       required
                                       maxlength="40"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('product_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Brand -->
                        <div>
                            <label for="brand_code" class="block text-sm font-medium text-gray-700">Brand *</label>
                            <div class="mt-1">
                                <select name="brand_code"
                                        id="brand_code"
                                        required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->brand_code }}" {{ old('brand_code') == $brand->brand_code ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('brand_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Distributor -->
                        <div>
                            <label for="distributor_code" class="block text-sm font-medium text-gray-700">Distributor *</label>
                            <div class="mt-1">
                                <select name="distributor_code"
                                        id="distributor_code"
                                        required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select Distributor</option>
                                    @foreach($distributors as $distributor)
                                        <option value="{{ $distributor->distributor_code }}" {{ old('distributor_code') == $distributor->distributor_code ? 'selected' : '' }}>
                                            {{ $distributor->distributor_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('distributor_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Entry Date -->
                        <div>
                            <label for="entry_date" class="block text-sm font-medium text-gray-700">Entry Date *</label>
                            <div class="mt-1">
                                <input type="date"
                                       name="entry_date"
                                       id="entry_date"
                                       value="{{ old('entry_date', date('Y-m-d')) }}"
                                       required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('entry_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Product Price -->
                        <div>
                            <label for="product_price" class="block text-sm font-medium text-gray-700">Product Price *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number"
                                       name="product_price"
                                       id="product_price"
                                       value="{{ old('product_price') }}"
                                       required
                                       min="0"
                                       step="1"
                                       placeholder="0"
                                       class="pl-10 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('product_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Quantity -->
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Stock Quantity *</label>
                            <div class="mt-1">
                                <input type="number"
                                       name="stock_quantity"
                                       id="stock_quantity"
                                       value="{{ old('stock_quantity', 0) }}"
                                       required
                                       min="0"
                                       step="1"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('stock_quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                            <div class="mt-1">
                                <textarea name="description"
                                          id="description"
                                          rows="3"
                                          required
                                          maxlength="200"
                                          placeholder="Describe the product features and details"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Pricing -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pricing</h3>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Cost Price -->
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number"
                                       name="cost_price"
                                       id="cost_price"
                                       value="{{ old('cost_price') }}"
                                       required
                                       min="0"
                                       step="1"
                                       placeholder="0"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('cost_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number"
                                       name="selling_price"
                                       id="selling_price"
                                       value="{{ old('selling_price') }}"
                                       required
                                       min="0"
                                       step="1"
                                       placeholder="0"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('selling_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Profit Margin Display -->
                        <div class="sm:col-span-2">
                            <div class="bg-gray-50 rounded-md p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Profit Margin:</span>
                                    <span id="profit-margin" class="text-sm font-bold text-green-600">Rp 0 (0%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Stock -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Stock Information</h3>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                        <!-- Current Stock -->
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Current Stock *</label>
                            <div class="mt-1">
                                <input type="number"
                                       name="stock_quantity"
                                       id="stock_quantity"
                                       value="{{ old('stock_quantity', 0) }}"
                                       required
                                       min="0"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('stock_quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Minimum Stock -->
                        <div>
                            <label for="min_stock" class="block text-sm font-medium text-gray-700">Minimum Stock</label>
                            <div class="mt-1">
                                <input type="number"
                                       name="min_stock"
                                       id="min_stock"
                                       value="{{ old('min_stock', 0) }}"
                                       min="0"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('min_stock')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Maximum Stock -->
                        <div>
                            <label for="max_stock" class="block text-sm font-medium text-gray-700">Maximum Stock *</label>
                            <div class="mt-1">
                                <input type="number"
                                       name="max_stock"
                                       id="max_stock"
                                       value="{{ old('max_stock', 1000) }}"
                                       min="0"
                                       required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('max_stock')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                            <div class="mt-1">
                                <select name="unit"
                                        id="unit"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="pcs" {{ old('unit', 'pcs') === 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                    <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                    <option value="g" {{ old('unit') === 'g' ? 'selected' : '' }}>Gram (g)</option>
                                    <option value="l" {{ old('unit') === 'l' ? 'selected' : '' }}>Liter (l)</option>
                                    <option value="ml" {{ old('unit') === 'ml' ? 'selected' : '' }}>Milliliter (ml)</option>
                                    <option value="box" {{ old('unit') === 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="pack" {{ old('unit') === 'pack' ? 'selected' : '' }}>Pack</option>
                                </select>
                            </div>
                            @error('unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Product Image -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Product Image</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Upload Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <div id="image-preview" class="hidden">
                                    <img id="preview-img" src="" alt="Preview" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                </div>
                                <div id="upload-placeholder">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="image" name="image" type="file" accept="image/*" class="sr-only" onchange="previewImage(this)">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Status -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Status</h3>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_active"
                                   name="is_active"
                                   type="checkbox"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Active Product</label>
                            <p class="text-gray-500">Product is available for sale</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('products.index') }}"
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Product
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
                document.getElementById('upload-placeholder').classList.add('hidden');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function calculateProfit() {
        const purchasePrice = parseFloat(document.getElementById('cost_price').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;

        const profit = sellingPrice - purchasePrice;
        const profitPercentage = purchasePrice > 0 ? (profit / purchasePrice * 100) : 0;

        const profitMarginElement = document.getElementById('profit-margin');
        const formattedProfit = new Intl.NumberFormat('id-ID').format(profit);
        const formattedPercentage = profitPercentage.toFixed(1);

        profitMarginElement.textContent = `Rp ${formattedProfit} (${formattedPercentage}%)`;

        if (profit < 0) {
            profitMarginElement.className = 'text-sm font-bold text-red-600';
        } else if (profit === 0) {
            profitMarginElement.className = 'text-sm font-bold text-gray-600';
        } else {
            profitMarginElement.className = 'text-sm font-bold text-green-600';
        }
    }

    function validateStockLimits() {
        const minStock = parseInt(document.getElementById('min_stock').value) || 0;
        const maxStock = parseInt(document.getElementById('max_stock').value) || 0;

        const maxStockInput = document.getElementById('max_stock');
        const errorMessage = maxStockInput.parentNode.parentNode.querySelector('.stock-error');

        if (errorMessage) {
            errorMessage.remove();
        }

        if (maxStock < minStock) {
            maxStockInput.classList.add('border-red-500');
            const error = document.createElement('p');
            error.className = 'mt-2 text-sm text-red-600 stock-error';
            error.textContent = 'Maximum stock must be greater than or equal to minimum stock';
            maxStockInput.parentNode.parentNode.appendChild(error);
        } else {
            maxStockInput.classList.remove('border-red-500');
        }
    }

    document.getElementById('cost_price').addEventListener('input', calculateProfit);
    document.getElementById('selling_price').addEventListener('input', calculateProfit);
    document.getElementById('min_stock').addEventListener('input', validateStockLimits);
    document.getElementById('max_stock').addEventListener('input', validateStockLimits);
</script>
@endpush
@endsection
