<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Brand::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('brand_name', 'like', "%{$search}%")
                  ->orWhere('brand_code', 'like', "%{$search}%");
            });
        }

        $brands = $query->latest()->paginate(15);

        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextCode = Brand::generateNextCode();
        return view('brands.create', compact('nextCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:30',
            'brand_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate brand code baru untuk handle race condition
            do {
                $brandCode = Brand::generateNextCode();
                $exists = Brand::where('brand_code', $brandCode)->exists();
            } while ($exists);

            $validated['brand_code'] = $brandCode;

            // Handle photo upload
            if ($request->hasFile('brand_photo')) {
                $photoPath = $request->file('brand_photo')->store('brands', 'public');
                $validated['brand_photo'] = basename($photoPath);
            } else {
                $validated['brand_photo'] = 'default.jpg';
            }

            $brand = Brand::create($validated);

            DB::commit();

            return redirect()->route('brands.index')
                ->with('success', 'Merek berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat merek: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand->load('products');

        $stats = [
            'total_products' => $brand->products->count(),
            'active_products' => $brand->products()->where('stock_quantity', '>', 0)->count(),
            'total_stock_value' => $brand->products->sum(function($product) {
                return $product->stock_quantity * $product->product_price;
            }),
            'low_stock_products' => $brand->products()->where('stock_quantity', '<', 10)->count(),
            'out_of_stock_products' => $brand->products()->where('stock_quantity', 0)->count(),
        ];

        return view('brands.show', compact('brand', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:30',
            'brand_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Handle photo upload
            if ($request->hasFile('brand_photo')) {
                // Delete old photo
                if ($brand->brand_photo && $brand->brand_photo !== 'default.jpg') {
                    Storage::disk('public')->delete('brands/' . $brand->brand_photo);
                }

                $photoPath = $request->file('brand_photo')->store('brands', 'public');
                $validated['brand_photo'] = basename($photoPath);
            }

            $brand->update($validated);

            DB::commit();

            return redirect()->route('brands.index')
                ->with('success', 'Merek berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui merek: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Check if brand has products
        if ($brand->products()->exists()) {
            return redirect()->back()
                ->with('error', 'Merek tidak dapat dihapus karena masih memiliki produk.');
        }

        DB::beginTransaction();
        try {
            // Delete photo
            if ($brand->brand_photo && $brand->brand_photo !== 'default.jpg') {
                Storage::disk('public')->delete('brands/' . $brand->brand_photo);
            }

            $brand->delete();

            DB::commit();

            return redirect()->route('brands.index')
                ->with('success', 'Merek berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus merek: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of the brand.
     */
    public function toggleStatus(Brand $brand)
    {
        DB::beginTransaction();
        try {
            $brand->update([
                'status' => $brand->status === 'Active' ? 'Inactive' : 'Active'
            ]);

            DB::commit();

            $message = $brand->status === 'Active' ? 'Brand activated successfully!' : 'Brand deactivated successfully!';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to toggle brand status: ' . $e->getMessage());
        }
    }
}
