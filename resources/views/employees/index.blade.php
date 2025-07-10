@extends('layouts.sidebar')

@section('title', 'Employees')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Employees
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage cashier employees and staff
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('employees.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Employee
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mt-6">
            <div class="px-4 py-3 border-b border-gray-200">
                <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search employees..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('employees.index') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Employees List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md mt-6">
            @if($employees->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($employees as $employee)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="ml-4">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900">{{ $employee->name }}</p>
                                                @if(!$employee->is_active)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($employee->role) }}
                                                </span>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-600">
                                                @if($employee->employee_id)
                                                    <p>ID: {{ $employee->employee_id }}</p>
                                                @endif
                                                <p>Email: {{ $employee->email }}</p>
                                                @if($employee->phone)
                                                    <p>Phone: {{ $employee->phone }}</p>
                                                @endif
                                                @if($employee->hire_date)
                                                    <p>Hired: {{ $employee->hire_date->format('M d, Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        @if($employee->salary)
                                            <div class="text-sm font-medium text-gray-900">
                                                Rp {{ number_format($employee->salary, 0, ',', '.') }}/month
                                            </div>
                                        @endif
                                        @if($employee->hire_date)
                                            <div class="text-sm text-gray-500">
                                                {{ $employee->hire_date->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex items-center space-x-2">
                                        <a href="{{ route('employees.show', $employee) }}"
                                           class="text-blue-600 hover:text-blue-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        <a href="{{ route('employees.edit', $employee) }}"
                                           class="text-yellow-600 hover:text-yellow-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('employees.toggle-status', $employee) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-gray-600 hover:text-gray-500"
                                                    onclick="return confirm('Are you sure you want to {{ $employee->is_active ? 'deactivate' : 'activate' }} this employee?')">
                                                @if($employee->is_active)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-2.647-2.647m0 0a9 9 0 01-12.728 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('employees.reset-password', $employee) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-purple-600 hover:text-purple-500"
                                                    onclick="return confirm('Are you sure you want to reset this employee password to default?')"
                                                    title="Reset Password">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m0 14a2 2 0 002-2m0 0a2 2 0 00-2-2m2 2a2 2 0 01-2 2M9 5a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
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
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No employees found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'status']))
                            Try adjusting your search or filter criteria.
                        @else
                            Get started by adding your first employee.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status']))
                        <div class="mt-6">
                            <a href="{{ route('employees.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Employee
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
