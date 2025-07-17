<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #2c3e50;
        }

        .header .company-info {
            font-size: 10px;
            color: #666;
            margin-bottom: 8px;
        }

        .header .period {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
        }

        .summary {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .summary h2 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 12px 0;
            color: #2c3e50;
            text-align: center;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 4px 8px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .summary-cell.label {
            font-weight: bold;
            width: 50%;
        }

        .summary-cell.value {
            text-align: right;
            width: 50%;
        }

        .transactions {
            margin-top: 15px;
        }

        .transactions h2 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 12px 0;
            color: #2c3e50;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .transaction-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
            border: 1px solid #2c3e50;
        }

        .transaction-table td {
            padding: 5px 4px;
            border: 1px solid #dee2e6;
            font-size: 9px;
            vertical-align: top;
        }

        .transaction-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #666;
        }

        .footer-info {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .item-detail {
            font-size: 8px;
            line-height: 1.3;
        }

        .item-detail .product-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .item-detail .product-qty {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="company-info">
            SISTEM INVENTORY MANAGEMENT<br>
            Laporan Penjualan Otomatis
        </div>
        <div class="period">
            Periode: {{ $summary['period_start'] }} - {{ $summary['period_end'] }}
        </div>
    </div>

    <div class="summary">
        <h2>RINGKASAN PENJUALAN</h2>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell label">Total Penjualan:</div>
                <div class="summary-cell value">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Total Transaksi:</div>
                <div class="summary-cell value">{{ number_format($summary['total_transactions']) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Total Item Terjual:</div>
                <div class="summary-cell value">{{ number_format($summary['total_items']) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Rata-rata Transaksi:</div>
                <div class="summary-cell value">Rp {{ number_format($summary['average_transaction'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="transactions">
        <h2>DETAIL TRANSAKSI</h2>

        @if($sales->count() > 0)
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">No Transaksi</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 8%;">Waktu</th>
                        <th style="width: 15%;">Kasir</th>
                        <th style="width: 8%;">Qty</th>
                        <th style="width: 15%;">Total</th>
                        <th style="width: 29%;">Detail Item</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td class="font-bold">{{ $sale->transaction_code }}</td>
                            <td>{{ $sale->purchase_date->format('d/m/Y') }}</td>
                            <td>{{ $sale->purchase_date->format('H:i') }}</td>
                            <td>{{ $sale->cashier->name }}</td>
                            <td class="text-center">{{ $sale->total_quantity }}</td>
                            <td class="text-right">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                            <td>
                                @if($sale->items && $sale->items->count() > 0)
                                    @foreach($sale->items as $item)
                                        <div class="item-detail">
                                            <span class="product-name">{{ $item->product->product_name }}</span><br>
                                            <span class="product-qty">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                        </div>
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                @else
                                    <em>Detail tidak tersedia</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada transaksi pada periode ini.
            </div>
        @endif
    </div>

    <div class="footer">
        <div class="footer-info">
            <div class="footer-left">
                <strong>Dibuat oleh:</strong> {{ $summary['generated_by'] }}<br>
                <strong>Tanggal cetak:</strong> {{ $summary['generated_at'] }}
            </div>
            <div class="footer-right">
                <strong>Inventory Management System</strong><br>
                Laporan dibuat secara otomatis
            </div>
        </div>
    </div>
</body>
</html>
