@extends('layouts.sidebar')

@section('title', 'Brands')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Brands
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage product brands and manufacturers
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('brands.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Brand
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mt-6">
            <div class="px-4 py-3 border-b border-gray-200">
                <form method="GET" action="{{ route('brands.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search brands..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('brands.index') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Brands Grid -->
        <div class="mt-6">
            @if($brands->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($brands as $brand)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <!-- Brand Logo -->
                                <div class="flex items-center justify-center h-24 mb-4">
                                    @if($brand->brand_photo && $brand->brand_photo !== 'default.jpg')
                                        <img class="max-h-20 max-w-full object-contain"
                                             src="{{ Storage::url('brands/' . $brand->brand_photo) }}"
                                             alt="{{ $brand->brand_name }}">
                                    @else
                                        <div class="h-20 w-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Brand Info -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $brand->brand_name }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $brand->brand_code }}</p>

                                    <!-- Product Count -->
                                    <p class="text-sm text-gray-500 mb-4">
                                        {{ $brand->products->count() }} {{ Str::plural('product', $brand->products->count()) }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('brands.show', $brand) }}"
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View
                                    </a>

                                    <a href="{{ route('brands.edit', $brand) }}"
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('brands.toggle-status', $brand) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                onclick="return confirm('Are you sure you want to {{ $brand->is_active ? 'deactivate' : 'activate' }} this brand?')">
                                            {{ $brand->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $brands->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-white shadow rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No brands found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'status']))
                            Try adjusting your search or filter criteria.
                        @else
                            Get started by adding your first brand.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status']))
                        <div class="mt-6">
                            <a href="{{ route('brands.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Brand
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
