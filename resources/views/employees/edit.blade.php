@extends('layouts.sidebar')

@section('title', 'Edit Employee')

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
                        <a href="{{ route('employees.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Employees</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-500">Edit {{ $employee->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Employee</h1>
            <p class="mt-2 text-sm text-gray-600">Update employee information</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white shadow rounded-lg">
                <!-- Personal Information -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Personal Information</h3>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $employee->name) }}"
                                       required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Employee ID -->
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="employee_id"
                                       id="employee_id"
                                       value="{{ old('employee_id', $employee->employee_id) }}"
                                       placeholder="e.g., EMP001"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('employee_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                            <div class="mt-1">
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $employee->email) }}"
                                       required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', $employee->phone) }}"
                                       placeholder="e.g., +6281234567890"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <div class="mt-1">
                                <textarea name="address"
                                          id="address"
                                          rows="3"
                                          placeholder="Enter full address"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('address', $employee->address) }}</textarea>
                            </div>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Employment Information -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Employment Information</h3>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                            <div class="mt-1">
                                <select name="role"
                                        id="role"
                                        required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select Role</option>
                                    <option value="admin" {{ old('role', $employee->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ old('role', $employee->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="cashier" {{ old('role', $employee->role) === 'cashier' ? 'selected' : '' }}>Cashier</option>
                                    <option value="staff" {{ old('role', $employee->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                            @error('role')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                            <div class="mt-1">
                                <input type="text"
                                       name="department"
                                       id="department"
                                       value="{{ old('department', $employee->department) }}"
                                       placeholder="e.g., Sales, Inventory, Management"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('department')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hire Date -->
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                            <div class="mt-1">
                                <input type="date"
                                       name="hire_date"
                                       id="hire_date"
                                       value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('hire_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Salary -->
                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700">Monthly Salary (Rp)</label>
                            <div class="mt-1">
                                <input type="number"
                                       name="salary"
                                       id="salary"
                                       value="{{ old('salary', $employee->salary) }}"
                                       min="0"
                                       step="1000"
                                       placeholder="e.g., 5000000"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('salary')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Password Change -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Change Password</h3>
                    <p class="text-sm text-gray-600 mb-4">Leave blank to keep current password</p>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <div class="mt-1">
                                <input type="password"
                                       name="password"
                                       id="password"
                                       minlength="8"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <div class="mt-1">
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Status and Notes -->
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Status & Notes</h3>

                    <div class="space-y-6">
                        <!-- Status -->
                        <div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_active"
                                           name="is_active"
                                           type="checkbox"
                                           value="1"
                                           {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_active" class="font-medium text-gray-700">Active Employee</label>
                                    <p class="text-gray-500">Employee can login and access the system</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <div class="mt-1">
                                <textarea name="notes"
                                          id="notes"
                                          rows="4"
                                          placeholder="Any additional notes about the employee..."
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('notes', $employee->notes) }}</textarea>
                            </div>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('employees.show', $employee) }}"
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Employee
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
