<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:manager');
    }

    /**
     * Display pending orders for approval
     */
    public function index()
    {
        $orders = Order::with(['creator', 'items'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('orders'));
    }

    /**
     * Show order details for approval
     */
    public function show(Order $order)
    {
        $order->load(['creator', 'items']);

        return view('approvals.show', compact('order'));
    }

    /**
     * Approve an order
     */
    public function approve(Order $order)
    {
        if (!$order->isPending()) {
            return redirect()->route('approvals.index')
                ->with('error', 'Order sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $oldValues = $order->toArray();

            // Update order status
            $order->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Create Purchase Order
            $purchaseOrder = PurchaseOrder::create([
                'order_id' => $order->id,
                'po_number' => PurchaseOrder::generatePONumber(),
                'total_amount' => $order->getTotalAmount(),
                'supplier_info' => 'To be filled by procurement team',
                'expected_delivery_date' => now()->addDays(7),
            ]);

            AuditLog::logAction('approved', $order, $oldValues, $order->fresh()->toArray());

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Order berhasil disetujui dan PO telah dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyetujui order: ' . $e->getMessage());
        }
    }

    /**
     * Reject an order
     */
    public function reject(Request $request, Order $order)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:1000',
        ], [
            'rejection_note.required' => 'Catatan penolakan wajib diisi.',
            'rejection_note.max' => 'Catatan penolakan maksimal 1000 karakter.',
        ]);

        if (!$order->isPending()) {
            return redirect()->route('approvals.index')
                ->with('error', 'Order sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $oldValues = $order->toArray();

            $order->update([
                'status' => 'rejected',
                'rejection_note' => $request->rejection_note,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            AuditLog::logAction('rejected', $order, $oldValues, $order->fresh()->toArray());

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Order berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menolak order: ' . $e->getMessage());
        }
    }
}
