@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Edit Product
                    </h2>
                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Product Code: {{ $product->product_code }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                        Back to Products
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('products.update', $product->product_code) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <!-- Basic Information -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Product Code -->
                        <div>
                            <label for="product_code" class="block text-sm font-medium text-gray-700">Product Code *</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="product_code"
                                       id="product_code"
                                       value="{{ old('product_code', $product->product_code) }}"
                                       required
                                       maxlength="20"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('product_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Product Name -->
                        <div>
                            <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="product_name"
                                       id="product_name"
                                       value="{{ old('product_name', $product->product_name) }}"
                                       required
                                       maxlength="255"
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
                                        <option value="{{ $brand->brand_code }}"
                                                {{ old('brand_code', $product->brand_code) == $brand->brand_code ? 'selected' : '' }}>
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
                                        <option value="{{ $distributor->distributor_code }}"
                                                {{ old('distributor_code', $product->distributor_code) == $distributor->distributor_code ? 'selected' : '' }}>
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
                                       value="{{ old('entry_date', $product->entry_date) }}"
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
                                       value="{{ old('product_price', $product->product_price) }}"
                                       required
                                       min="0"
                                       step="1"
                                       placeholder="0"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 sm:text-sm border-gray-300 rounded-md">
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
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}"
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
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="mt-1">
                                <textarea name="description"
                                          id="description"
                                          rows="3"
                                          placeholder="Enter product description..."
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $product->description) }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Product Image -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Product Image</h3>

                    <!-- Current Image -->
                    @if($product->image)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->product_name }}"
                                     class="h-20 w-20 object-cover rounded-lg border border-gray-300">
                                <div class="text-sm text-gray-500">
                                    <p>{{ basename($product->image) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Upload New Image (Optional)</label>
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

                <!-- Submit Buttons -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Product
                    </button>
                    <a href="{{ route('products.index') }}"
                       class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
