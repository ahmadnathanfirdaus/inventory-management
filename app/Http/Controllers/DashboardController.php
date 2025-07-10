<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceived;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isManager()) {
            return $this->managerDashboard();
        }

        abort(403, 'Unauthorized role');
    }

    private function adminDashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'approved_orders' => Order::where('status', 'approved')->count(),
            'rejected_orders' => Order::where('status', 'rejected')->count(),
            'total_pos' => PurchaseOrder::count(),
            'pending_pos' => PurchaseOrder::where('status', 'pending')->count(),
            'goods_received' => GoodsReceived::count(),
        ];

        $recent_orders = Order::with(['creator', 'items'])
            ->latest()
            ->take(5)
            ->get();

        $recent_goods = GoodsReceived::with(['purchaseOrder.order', 'receiver'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recent_orders', 'recent_goods'));
    }

    private function managerDashboard()
    {
        $stats = [
            'pending_approvals' => Order::where('status', 'pending')->count(),
            'approved_today' => Order::where('status', 'approved')
                ->whereDate('approved_at', today())
                ->count(),
            'total_approved' => Order::where('status', 'approved')->count(),
            'total_rejected' => Order::where('status', 'rejected')->count(),
        ];

        $pending_orders = Order::with(['creator', 'items'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.manager', compact('stats', 'pending_orders'));
    }
}
