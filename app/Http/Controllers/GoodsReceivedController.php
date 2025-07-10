<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsReceivedRequest;
use App\Models\GoodsReceived;
use App\Models\PurchaseOrder;
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
        $goodsReceived = GoodsReceived::with(['purchaseOrder.order', 'orderItem', 'receiver'])
            ->latest()
            ->paginate(10);

        return view('goods-received.index', compact('goodsReceived'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $purchaseOrders = PurchaseOrder::with(['order.items'])
            ->whereIn('status', ['pending', 'partial'])
            ->get();

        return view('goods-received.create', compact('purchaseOrders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GoodsReceivedRequest $request)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::where('po_number', $request->po_number)->first();

            foreach ($request->items as $item) {
                $orderItem = $purchaseOrder->order->items()->find($item['order_item_id']);

                $quantityShortage = $orderItem->quantity - $item['quantity_received'];
                $status = $quantityShortage > 0 ? 'incomplete' : 'complete';

                if ($item['quantity_received'] != $orderItem->quantity) {
                    $status = 'adjusted';
                }

                $goodsReceived = GoodsReceived::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'order_item_id' => $item['order_item_id'],
                    'item_name' => $orderItem->item_name,
                    'quantity_ordered' => $orderItem->quantity,
                    'quantity_received' => $item['quantity_received'],
                    'quantity_shortage' => $quantityShortage,
                    'status' => $status,
                    'notes' => $item['notes'] ?? null,
                    'received_by' => Auth::id(),
                    'received_at' => now(),
                ]);

                AuditLog::logAction('created', $goodsReceived, null, $goodsReceived->toArray());
            }

            // Update PO status
            $this->updatePurchaseOrderStatus($purchaseOrder);

            DB::commit();

            return redirect()->route('goods-received.index')
                ->with('success', 'Barang berhasil diterima dan dicatat!');
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
    public function show(GoodsReceived $goodsReceived)
    {
        $goodsReceived->load(['purchaseOrder.order', 'orderItem', 'receiver']);

        return view('goods-received.show', compact('goodsReceived'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodsReceived $goodsReceived)
    {
        $goodsReceived->load(['purchaseOrder.order', 'orderItem']);

        return view('goods-received.edit', compact('goodsReceived'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GoodsReceived $goodsReceived)
    {
        $request->validate([
            'quantity_received' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $goodsReceived->toArray();

            $quantityShortage = $goodsReceived->quantity_ordered - $request->quantity_received;
            $status = $quantityShortage > 0 ? 'incomplete' : 'complete';

            if ($request->quantity_received != $goodsReceived->quantity_ordered) {
                $status = 'adjusted';
            }

            $goodsReceived->update([
                'quantity_received' => $request->quantity_received,
                'quantity_shortage' => $quantityShortage,
                'status' => $status,
                'notes' => $request->notes,
            ]);

            AuditLog::logAction('updated', $goodsReceived, $oldValues, $goodsReceived->fresh()->toArray());

            // Update PO status
            $this->updatePurchaseOrderStatus($goodsReceived->purchaseOrder);

            DB::commit();

            return redirect()->route('goods-received.index')
                ->with('success', 'Data penerimaan barang berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsReceived $goodsReceived)
    {
        DB::beginTransaction();
        try {
            $oldValues = $goodsReceived->toArray();
            $purchaseOrder = $goodsReceived->purchaseOrder;

            AuditLog::logAction('deleted', $goodsReceived, $oldValues, null);

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
     * Get PO details for AJAX
     */
    public function getPODetails(Request $request)
    {
        $request->validate([
            'po_number' => 'required|string|exists:purchase_orders,po_number',
        ]);

        $purchaseOrder = PurchaseOrder::with(['order.items'])
            ->where('po_number', $request->po_number)
            ->first();

        if (!$purchaseOrder) {
            return response()->json(['error' => 'PO not found'], 404);
        }

        return response()->json([
            'po' => $purchaseOrder,
            'items' => $purchaseOrder->order->items,
        ]);
    }

    /**
     * Update Purchase Order status based on goods received
     */
    private function updatePurchaseOrderStatus(PurchaseOrder $purchaseOrder)
    {
        $totalItems = $purchaseOrder->order->items->count();
        $receivedItems = GoodsReceived::where('purchase_order_id', $purchaseOrder->id)->count();
        $completeItems = GoodsReceived::where('purchase_order_id', $purchaseOrder->id)
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
}
