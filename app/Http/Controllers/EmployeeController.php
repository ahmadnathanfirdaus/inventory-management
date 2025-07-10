<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:manager,admin');
    }

    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = User::employees();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $employees = $query->latest()->paginate(15);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'employee_id' => 'required|string|max:20|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $validated['user_code'] = User::generateNextCode();
            $validated['user_name'] = $validated['name']; // Set user_name same as name
            $validated['username'] = $validated['email']; // Set username same as email
            $validated['role'] = 'cashier';
            $validated['password'] = Hash::make($validated['password']);
            $validated['email_verified_at'] = now();

            $employee = User::create($validated);

            AuditLog::logAction('created', $employee, null, $employee->toArray());

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Pegawai berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan pegawai: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(User $employee)
    {
        if (!$employee->isCashier()) {
            abort(404);
        }

        $employee->load('sales');

        // Employee performance stats
        $totalSales = $employee->sales()->completed()->sum('total_amount');
        $totalTransactions = $employee->sales()->completed()->count();
        $avgTransactionValue = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Monthly performance
        $monthlySales = $employee->sales()
            ->completed()
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');

        $monthlyTransactions = $employee->sales()
            ->completed()
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->count();

        return view('employees.show', compact(
            'employee', 'totalSales', 'totalTransactions',
            'avgTransactionValue', 'monthlySales', 'monthlyTransactions'
        ));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(User $employee)
    {
        if (!$employee->isCashier()) {
            abort(404);
        }

        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, User $employee)
    {
        if (!$employee->isCashier()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'employee_id' => 'required|string|max:20|unique:users,employee_id,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $employee->toArray();

            $employee->update($validated);

            AuditLog::logAction('updated', $employee, $oldValues, $employee->fresh()->toArray());

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Data pegawai berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data pegawai: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Change employee password.
     */
    public function changePassword(Request $request, User $employee)
    {
        if (!$employee->isCashier()) {
            abort(404);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $employee->update([
                'password' => Hash::make($validated['password']),
            ]);

            AuditLog::logAction('password_changed', $employee, null, null);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Password pegawai berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengubah password: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate employee.
     */
    public function deactivate(User $employee)
    {
        if (!$employee->isCashier()) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $oldValues = $employee->toArray();

            $employee->update(['is_active' => false]);

            AuditLog::logAction('deactivated', $employee, $oldValues, $employee->fresh()->toArray());

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pegawai berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menonaktifkan pegawai: ' . $e->getMessage());
        }
    }

    /**
     * Activate employee.
     */
    public function activate(User $employee)
    {
        if (!$employee->isCashier()) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $oldValues = $employee->toArray();

            $employee->update(['is_active' => true]);

            AuditLog::logAction('activated', $employee, $oldValues, $employee->fresh()->toArray());

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pegawai berhasil diaktifkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengaktifkan pegawai: ' . $e->getMessage());
        }
    }
}
