<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->transaction_code }}</title>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .receipt {
            max-width: 300px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }

        .transaction-info {
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .items-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .item-row {
            border-bottom: 1px dotted #ccc;
            padding: 5px 0;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 11px;
        }

        .print-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Receipt</button>

    <div class="receipt">
        <div class="header">
            <div class="company-name">{{ config('app.name', 'Inventory System') }}</div>
            <div>Point of Sale System</div>
            <div class="receipt-title">SALES RECEIPT</div>
        </div>

        <div class="transaction-info">
            <div class="info-row">
                <span>Receipt #:</span>
                <span>{{ $sale->transaction_code }}</span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span>{{ $sale->purchase_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span>Cashier:</span>
                <span>{{ $sale->cashier->name ?? 'Unknown' }}</span>
            </div>
        </div>

        <div class="items-table">
            @if($sale->items && $sale->items->count() > 0)
                @foreach($sale->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ $item->product->name ?? 'Unknown Product' }}</div>
                    <div class="item-details">
                        <span>{{ number_format($item->quantity) }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="item-row">
                    <div class="item-name">No items found</div>
                </div>
            @endif
        </div>

        <div class="totals">
            <div class="total-row">
                <span>Total Items:</span>
                <span>{{ number_format($sale->total_quantity) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <div>Thank you for your purchase!</div>
            <div>{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <a href="{{ route('sales.show', $sale) }}" style="color: #007bff; text-decoration: none;">← Back to Sale Details</a>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
