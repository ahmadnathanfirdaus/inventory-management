<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Distributor::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('distributor_name', 'like', "%{$search}%")
                  ->orWhere('distributor_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $distributors = $query->latest()->paginate(15);

        return view('distributors.index', compact('distributors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextCode = Distributor::generateNextCode();
        return view('distributors.create', compact('nextCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'distributor_name' => 'required|string|max:40',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:13',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate distributor code baru untuk handle race condition
            do {
                $distributorCode = Distributor::generateNextCode();
                $exists = Distributor::where('distributor_code', $distributorCode)->exists();
            } while ($exists);

            $validated['distributor_code'] = $distributorCode;

            $distributor = Distributor::create($validated);

            DB::commit();

            return redirect()->route('distributors.index')
                ->with('success', 'Distributor berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat distributor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Distributor $distributor)
    {
        $distributor->load('products');
        $totalProducts = $distributor->products->count();

        return view('distributors.show', compact('distributor', 'totalProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributor $distributor)
    {
        return view('distributors.edit', compact('distributor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distributor $distributor)
    {
        $validated = $request->validate([
            'distributor_name' => 'required|string|max:40',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:13',
        ]);

        DB::beginTransaction();
        try {
            $distributor->update($validated);

            DB::commit();

            return redirect()->route('distributors.index')
                ->with('success', 'Distributor berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui distributor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distributor $distributor)
    {
        // Check if distributor has products
        if ($distributor->products()->exists()) {
            return redirect()->back()
                ->with('error', 'Distributor tidak dapat dihapus karena masih memiliki produk.');
        }

        DB::beginTransaction();
        try {
            $distributor->delete();

            DB::commit();

            return redirect()->route('distributors.index')
                ->with('success', 'Distributor berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus distributor: ' . $e->getMessage());
        }
    }
}
