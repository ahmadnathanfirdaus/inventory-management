<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Models\Brand;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        } elseif ($user->isCashier()) {
            return $this->cashierDashboard();
        }

        abort(403, 'Unauthorized role');
    }

    private function adminDashboard()
    {
        $stats = [
            'total_orders' => OrderRequest::count(),
            'pending_orders' => OrderRequest::where('status', 'Pending')->count(),
            'approved_orders' => OrderRequest::where('status', 'Approved')->count(),
            'rejected_orders' => OrderRequest::where('status', 'Rejected')->count(),
            'total_pos' => PurchaseOrder::count(),
            'pending_pos' => PurchaseOrder::where('status', 'Pending')->count(),
            'goods_received' => GoodsReceipt::count(),
            'total_products' => Product::count(),
            'total_brands' => Brand::count(),
            'total_distributors' => Distributor::count(),
        ];

        $recent_orders = OrderRequest::with(['admin'])
            ->latest()
            ->take(5)
            ->get();

        $recent_goods = GoodsReceipt::with(['admin', 'product'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recent_orders', 'recent_goods'));
    }

    private function managerDashboard()
    {
        $stats = [
            'pending_approvals' => OrderRequest::where('status', 'Pending')->count(),
            'approved_today' => OrderRequest::where('status', 'Approved')
                ->whereDate('updated_at', today())
                ->count(),
            'total_approved' => OrderRequest::where('status', 'Approved')->count(),
            'total_rejected' => OrderRequest::where('status', 'Rejected')->count(),
        ];

        $pending_orders = OrderRequest::with(['admin'])
            ->where('status', 'Pending')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.manager', compact('stats', 'pending_orders'));
    }

    private function cashierDashboard()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $user = Auth::user();

        // Stats for cashier - using purchase transactions as sales transactions
        $todaySales = Sale::whereDate('purchase_date', $today)
            ->sum('total_price');

        $todayTransactions = Sale::whereDate('purchase_date', $today)
            ->count();

        $mySalesThisMonth = Sale::where('user_code', $user->user_code)
            ->where('purchase_date', '>=', $thisMonth)
            ->sum('total_price');

        $productsAvailable = Product::count();

        // Recent transactions by this cashier (treating as sales)
        $recentSales = Sale::with('cashier')
            ->where('user_code', $user->user_code)
            ->latest()
            ->take(5)
            ->get();

        // Low stock products (for alert)
        $lowStockProducts = Product::where('stock_quantity', '<', 10)->get();

        return view('dashboard.cashier', compact(
            'todaySales', 'todayTransactions', 'mySalesThisMonth',
            'productsAvailable', 'recentSales', 'lowStockProducts'
        ));
    }
}
