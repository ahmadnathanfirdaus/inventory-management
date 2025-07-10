@extends('layouts.sidebar')

@section('title', 'Products')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Products
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your product inventory and stock levels
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('goods-received.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 5l7 7-7 7"/>
                    </svg>
                    Update Stock
                </a>
                <a href="{{ route('products.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Product
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mt-6">
            <div class="px-4 py-3 border-b border-gray-200">
                <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search products..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <select name="brand_code" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->brand_code }}" {{ request('brand_code') == $brand->brand_code ? 'selected' : '' }}>
                                {{ $brand->brand_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="distributor_code" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All Distributors</option>
                        @foreach($distributors as $distributor)
                            <option value="{{ $distributor->distributor_code }}" {{ request('distributor_code') == $distributor->distributor_code ? 'selected' : '' }}>
                                {{ $distributor->distributor_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'brand_id', 'status']))
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md mt-6">
            @if($products->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($products as $product)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0 h-16 w-16">
                                            @if($product->image && $product->image !== 'default.jpg')
                                                <img class="h-16 w-16 rounded-lg object-cover"
                                                     src="{{ Storage::url('products/' . $product->image) }}"
                                                     alt="{{ $product->product_name }}">
                                            @else
                                                <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="ml-4">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900">{{ $product->product_name }}</p>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-600">
                                                <p>{{ $product->brand->brand_name ?? 'No Brand' }} • Code: {{ $product->product_code }}</p>
                                                <p>Distributor: {{ $product->distributor->distributor_name ?? 'No Distributor' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock & Price Info -->
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($product->product_price, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Entry Date: {{ \Carbon\Carbon::parse($product->entry_date)->format('d/m/Y') }}
                                        </div>
                                        <div class="mt-1">
                                            @if($product->stock_quantity <= 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Out of Stock
                                                </span>
                                            @elseif($product->stock_quantity <= 10)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Low Stock ({{ $product->stock_quantity }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    In Stock ({{ $product->stock_quantity }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="ml-4 flex items-center space-x-2">
                                        <a href="{{ route('products.show', $product) }}"
                                           class="text-blue-600 hover:text-blue-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        <a href="{{ route('products.edit', $product) }}"
                                           class="text-yellow-600 hover:text-yellow-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-500"
                                                    onclick="return confirm('Are you sure you want to delete this product?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'brand_id', 'status']))
                            Try adjusting your search or filter criteria.
                        @else
                            Get started by adding your first product.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'brand_id', 'status']))
                        <div class="mt-6">
                            <a href="{{ route('products.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Product
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
