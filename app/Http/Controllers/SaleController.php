<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the authenticated user with proper typing
     */
    private function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dashboard statistics for POS
        $today = \Carbon\Carbon::today();

        // Get today's sales, but if no data for today, use latest available data
        $todaySales = Sale::whereDate('purchase_date', $today)->sum('total_price');
        $todayTransactions = Sale::whereDate('purchase_date', $today)->count();

        // If no data for today, get recent data (last 7 days)
        if ($todayTransactions == 0) {
            $recentDate = \Carbon\Carbon::now()->subDays(7);
            $todaySales = Sale::where('purchase_date', '>=', $recentDate)->sum('total_price');
            $todayTransactions = Sale::where('purchase_date', '>=', $recentDate)->count();
        }

        $avgTransaction = Sale::avg('total_price') ?? 0;

        $productsAvailable = Product::count();

        $query = Sale::with(['cashier']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%");
            });
        }

        // Filter by cashier (only for managers)
        /** @var User $user */
        $user = Auth::user();
        if ($request->filled('cashier_id') && $user && $user->canManageEmployees()) {
            $query->where('user_code', $request->cashier_id);
        } elseif ($user && $user->isCashier()) {
            // Cashiers can only see their own sales
            $query->where('user_code', $user->user_code);
        }
        // Admin and Manager can see all sales (no additional filter needed)

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        $sales = $query->latest('purchase_date')->paginate(15);

        $cashiers = $this->getAuthenticatedUser()->canManageEmployees()
            ? User::employees()->active()->orderBy('name')->get()
            : collect();

        // Recent sales for POS dashboard
        $recentSales = Sale::with('cashier')
            ->latest('purchase_date')
            ->take(5)
            ->get();

        // Check if this is POS route
        if ($request->route() && $request->route()->getName() === 'pos.index') {
            return view('pos.index', compact('sales', 'cashiers', 'todaySales', 'todayTransactions', 'avgTransaction', 'productsAvailable', 'recentSales'));
        }

        // For sales.index, also calculate total revenue
        $totalRevenue = Sale::sum('total_price');

        return view('sales.index', compact('sales', 'cashiers', 'todaySales', 'todayTransactions', 'avgTransaction', 'totalRevenue'));
    }

    /**
     * Show the form for creating a new resource (POS interface).
     */
    public function create()
    {
        // Only cashiers can create sales
        if (!$this->getAuthenticatedUser()->isCashier()) {
            abort(403, 'Hanya kasir yang dapat melakukan transaksi penjualan.');
        }

        $products = Product::with('brand')
            ->active()
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only cashiers can create sales
        if (!$this->getAuthenticatedUser()->isCashier()) {
            abort(403, 'Hanya kasir yang dapat melakukan transaksi penjualan.');
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_code',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }

            // Create sale
            $sale = Sale::create([
                'transaction_code' => Sale::generateNextCode(),
                'user_code' => Auth::user()->user_code,
                'total_quantity' => array_sum(array_column($validated['items'], 'quantity')),
                'total_price' => $totalAmount,
                'purchase_date' => now(),
            ]);

            // Create sale items and update stock
            foreach ($validated['items'] as $item) {
                $product = Product::where('product_code', $item['product_id'])->first();

                // Check stock availability
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Stok {$product->product_name} tidak mencukupi. Stok tersedia: {$product->stock_quantity}");
                }

                // Create sale item
                $sale->items()->create([
                    'product_code' => $product->product_code,
                    'quantity' => $item['quantity'],
                    'sub_total' => $item['price'] * $item['quantity'],
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            AuditLog::logAction('created', $sale, null, $sale->toArray());

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Transaksi berhasil! Silakan cetak struk.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        // Cashiers can only view their own sales unless they're managers
        if ($this->getAuthenticatedUser()->isCashier() && $sale->user_code !== Auth::user()->user_code) {
            abort(403, 'Anda hanya dapat melihat transaksi Anda sendiri.');
        }

        $sale->load(['cashier', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Print receipt
     */
    public function receipt(Sale $sale)
    {
        // Cashiers can only print their own receipts unless they're managers
        if ($this->getAuthenticatedUser()->isCashier() && $sale->user_code !== Auth::user()->user_code) {
            abort(403, 'Anda hanya dapat mencetak struk transaksi Anda sendiri.');
        }

        $sale->load(['cashier', 'items.product']);

        return view('sales.receipt', compact('sale'));
    }

    /**
     * Get product details for POS
     */
    public function getProduct(Product $product)
    {
        if (!$this->getAuthenticatedUser()->isCashier()) {
            abort(403);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->getCurrentPrice(),
            'stock' => $product->stock_quantity,
            'unit' => $product->unit,
            'brand' => $product->brand->name,
        ]);
    }

    /**
     * Search products for POS
     */
    public function searchProducts(Request $request)
    {
        if (!$this->getAuthenticatedUser()->isCashier()) {
            abort(403);
        }

        $search = $request->get('q');

        $products = Product::with('brand')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->active()
            ->where('stock_quantity', '>', 0)
            ->limit(10)
            ->get();

        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->getCurrentPrice(),
                'stock' => $product->stock_quantity,
                'unit' => $product->unit,
                'brand' => $product->brand->name,
            ];
        }));
    }

    /**
     * Daily sales report
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today());

        $query = Sale::with(['cashier', 'items'])
            ->whereDate('sale_date', $date)
            ->where('status', 'completed');

        // Filter by cashier for cashier role
        if ($this->getAuthenticatedUser()->isCashier()) {
            $query->where('cashier_id', Auth::id());
        }

        $sales = $query->get();
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $totalItems = $sales->sum('total_items');

        // Payment method breakdown
        $paymentMethods = $sales->groupBy('payment_method')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ];
        });

        return view('sales.daily-report', compact(
            'sales', 'date', 'totalSales', 'totalTransactions',
            'totalItems', 'paymentMethods'
        ));
    }

    /**
     * Void a sale transaction
     */
    public function voidSale(Sale $sale)
    {
        // Only cashiers can void sales, and only their own unless they're managers
        if ($this->getAuthenticatedUser()->isCashier() && $sale->user_code !== Auth::user()->user_code) {
            abort(403, 'Anda hanya dapat membatalkan transaksi Anda sendiri.');
        }

        try {
            DB::beginTransaction();

            // Restore product stock
            if ($sale->items) {
                foreach ($sale->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            // Delete the sale and its items
            $sale->items()->delete();
            $sale->delete();

            AuditLog::logAction('voided', $sale, $sale->toArray(), null);

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Transaksi berhasil dibatalkan dan stok produk telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a sale
     */
    public function edit(Sale $sale)
    {
        // Only managers and admins can edit sales
        if (!$this->getAuthenticatedUser()->canManageTransactions()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit transaksi.');
        }

        $sale->load(['cashier', 'items.product']);

        // Get all active products for the dropdown
        $products = Product::with('brand')
            ->active()
            ->orderBy('product_name')
            ->get();

        return view('sales.edit', compact('sale', 'products'));
    }

    /**
     * Update a sale
     */
    public function update(Request $request, Sale $sale)
    {
        // Only managers and admins can update sales
        if (!$this->getAuthenticatedUser()->canManageTransactions()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit transaksi.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_code',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // First, revert stock from original transaction
            foreach ($sale->items as $item) {
                $product = Product::where('product_code', $item->product_code)->first();
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            // Delete old items
            $sale->items()->delete();

            // Create new items and update stock
            $totalAmount = 0;
            $totalItems = 0;

            foreach ($request->items as $itemData) {
                $product = Product::where('product_code', $itemData['product_id'])->first();

                if (!$product) {
                    throw new \Exception("Product not found: {$itemData['product_id']}");
                }

                // Check stock availability
                if ($product->stock_quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->product_name}");
                }

                // Create sale item
                $transactionItem = new TransactionItem();
                $transactionItem->transaction_code = $sale->transaction_code;
                $transactionItem->product_code = $product->product_code;
                $transactionItem->quantity = $itemData['quantity'];
                $transactionItem->sub_total = $itemData['quantity'] * $itemData['price'];
                $transactionItem->save();

                // Update product stock
                $product->decrement('stock_quantity', $itemData['quantity']);

                // Add to totals
                $totalAmount += $transactionItem->sub_total;
                $totalItems += $itemData['quantity'];
            }

            // Update sale totals
            $sale->update([
                'total_price' => $totalAmount,
                'total_quantity' => $totalItems,
            ]);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Print receipt for a sale
     */
    public function printReceipt(Sale $sale)
    {
        // Same authorization as receipt method
        if ($this->getAuthenticatedUser()->isCashier() && $sale->user_code !== Auth::user()->user_code) {
            abort(403, 'Anda hanya dapat mencetak struk transaksi Anda sendiri.');
        }

        $sale->load(['cashier', 'items.product']);

        return view('sales.receipt', compact('sale'));
    }

    /**
     * Export sales data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'daily');

        $now = Carbon::now();

        // Determine date range based on type
        switch ($type) {
            case 'daily':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $filename = 'laporan-penjualan-harian-' . $now->format('Y-m-d');
                $title = 'Laporan Penjualan Harian - ' . $now->format('d F Y');
                break;

            case 'weekly':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $filename = 'laporan-penjualan-mingguan-' . $now->format('Y-W');
                $title = 'Laporan Penjualan Mingguan - ' . $startDate->format('d F') . ' s/d ' . $endDate->format('d F Y');
                break;

            case 'monthly':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $filename = 'laporan-penjualan-bulanan-' . $now->format('Y-m');
                $title = 'Laporan Penjualan Bulanan - ' . $now->format('F Y');
                break;

            default:
                return redirect()->back()->with('error', 'Tipe export tidak valid');
        }

        // Get sales data
        $query = Sale::with(['cashier', 'items.product'])
            ->whereBetween('purchase_date', [$startDate, $endDate]);

        // Filter by user role
        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->isCashier()) {
            $query->where('user_code', $user->user_code);
        }

        $sales = $query->orderBy('purchase_date', 'desc')->get();

        // Calculate summary
        $summary = [
            'total_sales' => $sales->sum('total_price'),
            'total_transactions' => $sales->count(),
            'total_items' => $sales->sum('total_quantity'),
            'average_transaction' => $sales->count() > 0 ? $sales->sum('total_price') / $sales->count() : 0,
            'period_start' => $startDate->format('d F Y'),
            'period_end' => $endDate->format('d F Y'),
            'generated_at' => $now->format('d F Y H:i:s'),
            'generated_by' => $user->name,
        ];

        return $this->exportToPDF($sales, $summary, $title, $filename);
    }

    /**
     * Export to PDF format
     */
    private function exportToPDF($sales, $summary, $title, $filename)
    {
        $data = [
            'sales' => $sales,
            'summary' => $summary,
            'title' => $title
        ];

        $pdf = Pdf::loadView('sales.export.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filename . '.pdf');
    }
}
