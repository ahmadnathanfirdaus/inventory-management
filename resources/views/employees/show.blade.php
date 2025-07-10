@extends('layouts.sidebar')

@section('title', $employee->name)

@section('content')
<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <a href="{{ route('employees.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Employees</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-500">{{ $employee->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                        <span class="text-xl font-medium text-gray-700">
                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $employee->name }}</h1>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($employee->role) }}
                            </span>
                            @if($employee->is_active)
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('employees.edit', $employee) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Employee
                </a>

                <form method="POST" action="{{ route('employees.toggle-status', $employee) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $employee->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $employee->is_active ? 'red' : 'green' }}-500"
                            onclick="return confirm('Are you sure you want to {{ $employee->is_active ? 'deactivate' : 'activate' }} this employee?')">
                        @if($employee->is_active)
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-2.647-2.647m0 0a9 9 0 01-12.728 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Deactivate
                        @else
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Activate
                        @endif
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Employee Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Personal Information</h3>

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Employee ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->employee_id ?: 'Not set' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->email }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone ?: 'Not provided' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->department ?: 'Not assigned' }}</dd>
                            </div>

                            @if($employee->address)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->address }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Employment Information</h3>

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Role</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($employee->role) }}</dd>
                            </div>

                            @if($employee->hire_date)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Hire Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->hire_date->format('F d, Y') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Years of Service</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->hire_date->diffInYears(now()) }} years, {{ $employee->hire_date->diffInMonths(now()) % 12 }} months</dd>
                                </div>
                            @endif

                            @if($employee->salary)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Monthly Salary</dt>
                                    <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($employee->salary, 0, ',', '.') }}</dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Account Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->created_at->format('F d, Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->updated_at->format('F d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($employee->notes)
                    <!-- Notes -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notes</h3>
                            <p class="text-sm text-gray-600">{{ $employee->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>

                        <div class="space-y-3">
                            <form method="POST" action="{{ route('employees.reset-password', $employee) }}" class="w-full">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        onclick="return confirm('Are you sure you want to reset this employee password to default?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m0 14a2 2 0 002-2m0 0a2 2 0 00-2-2m2 2a2 2 0 01-2 2M9 5a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
                                    </svg>
                                    Reset Password
                                </button>
                            </form>

                            <a href="mailto:{{ $employee->email }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Send Email
                            </a>

                            @if($employee->phone)
                                <a href="tel:{{ $employee->phone }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Call Phone
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Performance Stats (placeholder for future implementation) -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Overview</h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Sales Handled</span>
                                <span class="text-sm text-gray-900">0</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Total Revenue</span>
                                <span class="text-sm text-gray-900">Rp 0</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Average per Sale</span>
                                <span class="text-sm text-gray-900">Rp 0</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Last Sale</span>
                                <span class="text-sm text-gray-900">Never</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500">Performance data will be available once the employee starts making sales.</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity (placeholder for future implementation) -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Activity</h3>

                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No recent activity</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
