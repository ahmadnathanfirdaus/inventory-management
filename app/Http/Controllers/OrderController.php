<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest as OrderRequestValidation;
use App\Models\OrderRequest;
use App\Models\OrderRequestItem;
use App\Models\Product;
use App\Models\Distributor;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = OrderRequest::with(['admin', 'orderRequestItems'])
            ->where('admin_code', Auth::user()->user_code)
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextCode = OrderRequest::generateNextCode();
        $products = Product::with(['brand', 'distributor'])
                           ->where('stock_quantity', '>', 0)
                           ->orderBy('product_name')
                           ->get();
        $distributors = Distributor::orderBy('distributor_name')->get();

        return view('orders.create', compact('nextCode', 'products', 'distributors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequestValidation $request)
    {
        DB::beginTransaction();
        try {
            // Auto-generate order code baru untuk handle race condition
            do {
                $orderCode = OrderRequest::generateNextCode();
                $exists = OrderRequest::where('order_code', $orderCode)->exists();
            } while ($exists);

            $order = OrderRequest::create([
                'order_code' => $orderCode,
                'order_date' => $request->order_date ?: now()->format('Y-m-d'), // Gunakan input user atau default ke hari ini
                'notes' => $request->description, // Fix: gunakan 'notes' sesuai schema database
                'admin_code' => Auth::user()->user_code, // Fix: tambah admin_code
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                // Get product details to extract brand code
                $product = Product::find($item['product_code']);

                // Generate unique order item code
                $orderItemCode = OrderRequestItem::generateNextCode();

                OrderRequestItem::create([
                    'order_item_code' => $orderItemCode,
                    'order_code' => $order->order_code,
                    'product_code' => $item['product_code'],
                    'brand_code' => $product->brand_code,
                    'distributor_code' => $item['distributor_code'] ?? $product->distributor_code, // Use form input or product default
                    'order_quantity' => $item['quantity'],
                    'estimated_price' => $item['unit_price'] * $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderRequest $order)
    {
        $order->load(['admin', 'manager', 'orderRequestItems.product']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderRequest $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Order yang sudah diproses tidak dapat diedit.');
        }

        $order->load('orderRequestItems');

        // Get products and distributors for dropdowns
        $products = Product::with('brand', 'distributor')->active()->orderBy('product_name')->get();
        $distributors = Distributor::active()->orderBy('distributor_name')->get();

        return view('orders.edit', compact('order', 'products', 'distributors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderRequest $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Order yang sudah diproses tidak dapat diedit.');
        }

        // Validate the request
        $request->validate([
            'order_date' => 'required|date',
            'description' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_code' => 'required|exists:products,product_code',
            'items.*.distributor_code' => 'required|exists:distributors,distributor_code',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update order information
            $order->update([
                'order_date' => $request->order_date,
                'notes' => $request->description,
            ]);

            // Delete existing items
            $order->orderRequestItems()->delete();

            // Create new items
            foreach ($request->items as $item) {
                // Get product details to extract brand code
                $product = Product::find($item['product_code']);

                // Generate unique order item code
                $orderItemCode = OrderRequestItem::generateNextCode();

                OrderRequestItem::create([
                    'order_item_code' => $orderItemCode,
                    'order_code' => $order->order_code,
                    'product_code' => $item['product_code'],
                    'brand_code' => $product->brand_code,
                    'distributor_code' => $item['distributor_code'],
                    'order_quantity' => $item['quantity'],
                    'estimated_price' => $item['unit_price'] * $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderRequest $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Order yang sudah diproses tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus order: ' . $e->getMessage());
        }
    }
}
