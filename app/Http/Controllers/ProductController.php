<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
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
        $query = Product::with(['brand', 'distributor']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by brand
        if ($request->filled('brand_code')) {
            $query->where('brand_code', $request->brand_code);
        }

        // Filter by distributor
        if ($request->filled('distributor_code')) {
            $query->where('distributor_code', $request->distributor_code);
        }

        $products = $query->latest()->paginate(15);
        $brands = Brand::orderBy('brand_name')->get();
        $distributors = Distributor::orderBy('distributor_name')->get();

        return view('products.index', compact('products', 'brands', 'distributors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextCode = Product::generateNextCode();
        $brands = Brand::orderBy('brand_name')->get();
        $distributors = Distributor::orderBy('distributor_name')->get();
        return view('products.create', compact('nextCode', 'brands', 'distributors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:40',
            'brand_code' => 'required|exists:brands,brand_code',
            'distributor_code' => 'required|exists:distributors,distributor_code',
            'entry_date' => 'required|date',
            'product_price' => 'required|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate product code baru untuk handle race condition
            do {
                $productCode = Product::generateNextCode();
                $exists = Product::where('product_code', $productCode)->exists();
            } while ($exists);

            $validated['product_code'] = $productCode;
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $validated['image'] = basename($imagePath);
            } else {
                $validated['image'] = 'default.jpg';
            }

            $product = Product::create($validated);

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['brand', 'distributor']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $brands = Brand::orderBy('brand_name')->get();
        $distributors = Distributor::orderBy('distributor_name')->get();
        return view('products.edit', compact('product', 'brands', 'distributors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:40',
            'brand_code' => 'required|exists:brands,brand_code',
            'distributor_code' => 'required|exists:distributors,distributor_code',
            'entry_date' => 'required|date',
            'product_price' => 'required|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image && $product->image !== 'default.jpg') {
                    Storage::disk('public')->delete('products/' . $product->image);
                }

                $imagePath = $request->file('image')->store('products', 'public');
                $validated['image'] = basename($imagePath);
            }

            $product->update($validated);

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete image
            if ($product->image && $product->image !== 'default.jpg') {
                Storage::disk('public')->delete('products/' . $product->image);
            }

            $product->delete();

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
