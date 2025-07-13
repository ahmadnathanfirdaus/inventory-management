<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\OrderRequest;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodsReceivedController extends Controller
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
        $goodsReceived = GoodsReceipt::with(['purchaseOrder.orderRequest.admin', 'admin', 'product'])
            ->latest()
            ->paginate(10);

        return view('goods-received.index', compact('goodsReceived'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('goods-received.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_code' => [
                'required',
                'exists:order_requests,order_code',
                function ($attribute, $value, $fail) {
                    $orderRequest = OrderRequest::where('order_code', $value)->first();
                    if ($orderRequest && $orderRequest->hasGoodsReceipts()) {
                        $fail('Order ini sudah memiliki goods receipt. Tidak dapat diinput kembali.');
                    }
                }
            ],
            'items' => 'required|array',
            'items.*.product_code' => 'required|exists:products,product_code',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.status' => 'required|in:complete,partial,damaged',
        ]);

        DB::beginTransaction();
        try {
            $order = OrderRequest::with('orderRequestItems')->where('order_code', $request->order_code)->first();

            // Find the related purchase order
            $purchaseOrder = PurchaseOrder::where('order_code', $order->order_code)->first();

            if (!$purchaseOrder) {
                throw new \Exception('Purchase Order not found for this order. Please ensure the order is properly approved.');
            }

            foreach ($request->items as $item) {
                // Find the corresponding order item to get the estimated price
                $orderItem = $order->orderRequestItems->where('product_code', $item['product_code'])->first();

                // Create goods receipt record
                $goodsReceived = GoodsReceipt::create([
                    'receipt_code' => GoodsReceipt::generateNextCode(),
                    'po_code' => $purchaseOrder->po_code,
                    'product_code' => $item['product_code'],
                    'received_quantity' => $item['quantity_received'],
                    'actual_price' => $orderItem ? $orderItem->estimated_price : 0,
                    'received_date' => now()->format('Y-m-d'),
                    'admin_code' => Auth::user()->user_code,
                    'notes' => 'Status: ' . $item['status'],
                ]);

                // Update product stock automatically
                if ($item['status'] !== 'damaged') {
                    $product = Product::where('product_code', $item['product_code'])->first();
                    if ($product) {
                        $product->increment('stock_quantity', $item['quantity_received']);
                    }
                }
            }

            DB::commit();

            return redirect()->route('goods-received.index')
                ->with('success', 'Barang berhasil diterima dan stock telah diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mencatat penerimaan barang: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodsReceipt $goodsReceived)
    {
        $goodsReceived->load(['purchaseOrder.orderRequest', 'product', 'admin']);

        return view('goods-received.show', compact('goodsReceived'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsReceipt $goodsReceived)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $goodsReceived->purchaseOrder;

            $goodsReceived->delete();

            // Update PO status
            $this->updatePurchaseOrderStatus($purchaseOrder);

            DB::commit();

            return redirect()->route('goods-received.index')
                ->with('success', 'Data penerimaan barang berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Get Order details for AJAX
     */
    public function getOrderDetails(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string|exists:order_requests,order_code',
        ]);

        $order = OrderRequest::with(['orderRequestItems.product', 'admin', 'purchaseOrder'])
            ->where('order_code', trim($request->order_code))
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or not approved'], 404);
        }

        // Check if order already has goods receipts
        if ($order->hasGoodsReceipts()) {
            return response()->json([
                'error' => 'Order ini sudah memiliki goods receipt dan tidak dapat diinput kembali.'
            ], 422);
        }

        // Check if order has purchase order
        if (!$order->purchaseOrder) {
            return response()->json([
                'error' => 'Order ini belum memiliki Purchase Order. Pastikan order sudah disetujui oleh manager.'
            ], 422);
        }

        return response()->json([
            'order' => $order,
            'items' => $order->orderRequestItems,
            'purchase_order' => $order->purchaseOrder,
        ]);
    }

    /**
     * Update Purchase Order status based on goods received
     */
    private function updatePurchaseOrderStatus(PurchaseOrder $purchaseOrder)
    {
        $totalItems = $purchaseOrder->orderRequest->orderRequestItems->count();
        $receivedItems = GoodsReceipt::where('po_code', $purchaseOrder->po_code)->count();
        $completeItems = GoodsReceipt::where('po_code', $purchaseOrder->po_code)
            ->where('status', 'complete')
            ->count();

        if ($receivedItems === 0) {
            $status = 'pending';
        } elseif ($completeItems === $totalItems) {
            $status = 'completed';
        } else {
            $status = 'partial';
        }

        $purchaseOrder->update(['status' => $status]);
    }

    /**
     * API method to get available orders (without goods receipts)
     */
    public function getAvailableOrders()
    {
        try {
            $orders = OrderRequest::with(['admin', 'purchaseOrder'])
                ->withoutGoodsReceipts()
                ->where('status', 'approved')
                ->whereHas('purchaseOrder')
                ->latest()
                ->take(20)
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders->map(function ($order) {
                    return [
                        'order_code' => $order->order_code,
                        'order_date' => $order->order_date->format('d/m/Y'),
                        'admin' => $order->admin->name,
                        'po_code' => $order->purchaseOrder->po_code,
                        'po_number' => $order->purchaseOrder->po_number,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
