<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:manager,admin');
    }

    /**
     * Reports dashboard
     */
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Quick stats
        $stats = [
            'today_sales' => Sale::completed()->whereDate('sale_date', $today)->sum('total_amount'),
            'today_transactions' => Sale::completed()->whereDate('sale_date', $today)->count(),
            'month_sales' => Sale::completed()->where('sale_date', '>=', $thisMonth)->sum('total_amount'),
            'low_stock_products' => Product::lowStock()->count(),
            'out_of_stock_products' => Product::outOfStock()->count(),
            'total_products' => Product::active()->count(),
            'active_employees' => User::employees()->active()->count(),
        ];

        return view('reports.index', compact('stats'));
    }

    /**
     * Sales report by period
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $groupBy = $request->get('group_by', 'daily'); // daily, weekly, monthly

        $query = Sale::with(['cashier', 'items'])
            ->completed()
            ->whereBetween('sale_date', [$startDate, $endDate]);

        // Group by cashier if specified
        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }

        $sales = $query->get();

        // Group sales by period
        $groupedSales = $this->groupSalesByPeriod($sales, $groupBy);

        // Summary statistics
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $totalItems = $sales->sum('total_items');
        $avgTransactionValue = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Top products
        $topProducts = $this->getTopProducts($startDate, $endDate, 10);

        // Payment method breakdown
        $paymentMethods = $sales->groupBy('payment_method')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ];
        });

        $cashiers = User::employees()->active()->orderBy('name')->get();

        return view('reports.sales', compact(
            'sales', 'groupedSales', 'startDate', 'endDate', 'groupBy',
            'totalSales', 'totalTransactions', 'totalItems', 'avgTransactionValue',
            'topProducts', 'paymentMethods', 'cashiers'
        ));
    }

    /**
     * Product report
     */
    public function productReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Get products with sales data
        $products = Product::with(['brand', 'saleItems' => function($query) use ($startDate, $endDate) {
            $query->whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->completed()->whereBetween('sale_date', [$startDate, $endDate]);
            });
        }])->get();

        // Calculate metrics for each product
        $productStats = $products->map(function($product) {
            $saleItems = $product->saleItems;

            return [
                'product' => $product,
                'quantity_sold' => $saleItems->sum('quantity'),
                'total_revenue' => $saleItems->sum('total_price'),
                'total_profit' => $saleItems->sum(function($item) use ($product) {
                    return ($item->effective_price - $product->cost_price) * $item->quantity;
                }),
                'current_stock' => $product->stock_quantity,
                'stock_value' => $product->stock_quantity * $product->cost_price,
            ];
        })->sortByDesc('total_revenue');

        return view('reports.products', compact('productStats', 'startDate', 'endDate'));
    }

    /**
     * Low stock report
     */
    public function lowStockReport()
    {
        $lowStockProducts = Product::with('brand')
            ->lowStock()
            ->active()
            ->orderBy('stock_quantity')
            ->get();

        $outOfStockProducts = Product::with('brand')
            ->outOfStock()
            ->active()
            ->orderBy('name')
            ->get();

        return view('reports.low-stock', compact('lowStockProducts', 'outOfStockProducts'));
    }

    /**
     * Stock movement report
     */
    public function stockMovementReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $productId = $request->get('product_id');
        $type = $request->get('type');

        $query = StockMovement::with(['product.brand', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $movements = $query->latest()->paginate(50);

        $products = Product::active()->orderBy('name')->get();

        // Summary by type
        $summaryByType = StockMovement::whereBetween('created_at', [$startDate, $endDate])
            ->when($productId, function($q) use ($productId) {
                return $q->where('product_id', $productId);
            })
            ->selectRaw('type, SUM(quantity) as total_quantity, COUNT(*) as total_movements')
            ->groupBy('type')
            ->get();

        return view('reports.stock-movements', compact(
            'movements', 'products', 'startDate', 'endDate',
            'productId', 'type', 'summaryByType'
        ));
    }

    /**
     * Employee performance report
     */
    public function employeeReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $employees = User::employees()
            ->active()
            ->with(['sales' => function($query) use ($startDate, $endDate) {
                $query->completed()->whereBetween('sale_date', [$startDate, $endDate]);
            }])
            ->get();

        // Calculate performance metrics
        $employeeStats = $employees->map(function($employee) {
            $sales = $employee->sales;

            return [
                'employee' => $employee,
                'total_sales' => $sales->sum('total_amount'),
                'total_transactions' => $sales->count(),
                'total_items' => $sales->sum('total_items'),
                'avg_transaction_value' => $sales->count() > 0 ? $sales->sum('total_amount') / $sales->count() : 0,
            ];
        })->sortByDesc('total_sales');

        return view('reports.employees', compact('employeeStats', 'startDate', 'endDate'));
    }

    /**
     * Export sales report to CSV
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $sales = Sale::with(['cashier', 'items.product'])
            ->completed()
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->get();

        $filename = "sales_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Sale Number', 'Date', 'Cashier', 'Customer', 'Items Count',
                'Subtotal', 'Tax', 'Discount', 'Total', 'Payment Method', 'Status'
            ]);

            // Data rows
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->sale_number,
                    $sale->sale_date->format('Y-m-d H:i:s'),
                    $sale->cashier->name,
                    $sale->customer_display_name,
                    $sale->total_items,
                    $sale->subtotal,
                    $sale->tax_amount,
                    $sale->discount_amount,
                    $sale->total_amount,
                    $sale->payment_method,
                    $sale->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Group sales by period
     */
    private function groupSalesByPeriod($sales, $groupBy)
    {
        switch ($groupBy) {
            case 'weekly':
                return $sales->groupBy(function($sale) {
                    return $sale->sale_date->format('Y-W');
                });
            case 'monthly':
                return $sales->groupBy(function($sale) {
                    return $sale->sale_date->format('Y-m');
                });
            default: // daily
                return $sales->groupBy(function($sale) {
                    return $sale->sale_date->format('Y-m-d');
                });
        }
    }

    /**
     * Get top selling products
     */
    private function getTopProducts($startDate, $endDate, $limit = 10)
    {
        return SaleItem::with(['product.brand', 'sale'])
            ->whereHas('sale', function($query) use ($startDate, $endDate) {
                $query->completed()->whereBetween('sale_date', [$startDate, $endDate]);
            })
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * Export general reports
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');

        // For now, redirect to the sales export
        // You can expand this to handle different report types
        return $this->exportSales($request);
    }
}
