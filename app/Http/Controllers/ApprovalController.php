<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
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
        $orders = OrderRequest::with(['admin', 'orderRequestItems'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('orders'));
    }

    /**
     * Display the specified resource for review.
     */
    public function show(OrderRequest $order)
    {
        $order->load(['admin', 'orderRequestItems.product']);

        return view('approvals.show', compact('order'));
    }

    /**
     * Approve the order request.
     */
    public function approve(OrderRequest $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('approvals.index')
                ->with('error', 'Order can only be approved when status is pending.');
        }

        DB::beginTransaction();
        try {
            $oldValues = $order->toArray();

            $order->update([
                'status' => 'approved',
                'manager_code' => Auth::user()->user_code,
                'approval_date' => now()->format('Y-m-d'),
            ]);

            // Create Purchase Order
            $poCode = PurchaseOrder::generateNextCode();
            $purchaseOrder = PurchaseOrder::create([
                'po_code' => $poCode,
                'po_number' => $poCode, // Use same value for po_number
                'order_code' => $order->order_code,
                'manager_code' => Auth::user()->user_code,
                'po_date' => now()->format('Y-m-d'),
                'status' => 'pending',
                'total_estimated' => $order->getTotalAmount(),
            ]);

            // Log audit
            AuditLog::logAction('approved', $order, $oldValues, $order->fresh()->toArray());

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Order approved successfully! Purchase Order ' . $purchaseOrder->po_code . ' has been created.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to approve order: ' . $e->getMessage());
        }
    }

    /**
     * Reject the order request.
     */
    public function reject(Request $request, OrderRequest $order)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.'
        ]);

        if ($order->status !== 'pending') {
            return redirect()->route('approvals.index')
                ->with('error', 'Order can only be rejected when status is pending.');
        }

        DB::beginTransaction();
        try {
            $oldValues = $order->toArray();

            $order->update([
                'status' => 'rejected',
                'manager_code' => Auth::user()->user_code,
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Log audit
            AuditLog::logAction('rejected', $order, $oldValues, $order->fresh()->toArray());

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Order rejected successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to reject order: ' . $e->getMessage());
        }
    }
}
