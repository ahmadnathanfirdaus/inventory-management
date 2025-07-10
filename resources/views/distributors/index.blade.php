@extends('layouts.sidebar')

@section('title', 'Distributors')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Distributors
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage suppliers and distributors
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('distributors.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Distributor
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mt-6">
            <div class="px-4 py-3 border-b border-gray-200">
                <form method="GET" action="{{ route('distributors.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search distributors..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('distributors.index') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Distributors List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md mt-6">
            @if($distributors->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($distributors as $distributor)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="ml-4">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900">{{ $distributor->distributor_name }}</p>
                                                <span class="ml-2 text-xs text-gray-500 font-mono">{{ $distributor->distributor_code }}</span>
                                                @if($distributor->status === 'Inactive')
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-1 text-sm text-gray-600">
                                                @if($distributor->phone)
                                                    <p>Phone: {{ $distributor->phone }}</p>
                                                @endif
                                                @if($distributor->email)
                                                    <p>Email: {{ $distributor->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        @if($distributor->address)
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ Str::limit($distributor->address, 50) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex items-center space-x-2">
                                        <a href="{{ route('distributors.show', $distributor->distributor_code) }}"
                                           class="text-blue-600 hover:text-blue-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        <a href="{{ route('distributors.edit', $distributor->distributor_code) }}"
                                           class="text-yellow-600 hover:text-yellow-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('distributors.destroy', $distributor->distributor_code) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-500"
                                                    onclick="return confirm('Are you sure you want to delete this distributor?')">
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
                    {{ $distributors->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No distributors found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'status']))
                            Try adjusting your search or filter criteria.
                        @else
                            Get started by adding your first distributor.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status']))
                        <div class="mt-6">
                            <a href="{{ route('distributors.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Distributor
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
