@extends('layouts.sidebar')

@section('title', 'Goods Received Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Goods Received #{{ $goodsReceived->receipt_code }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('goods-received.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Receipt Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Purchase Order</dt>
                        <dd class="mt-1 text-sm text-gray-900">#{{ $goodsReceived->po_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Product</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->product->product_name ?? $goodsReceived->product_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Received Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->received_date ? $goodsReceived->received_date->format('M d, Y') : 'Not Set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Received Quantity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($goodsReceived->received_quantity) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($goodsReceived->actual_price, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Value</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">Rp {{ number_format($goodsReceived->actual_price * $goodsReceived->received_quantity, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Received By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->admin->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Recorded At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($goodsReceived->notes)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Received Item Details</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product Code
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity Received
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Value
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $goodsReceived->product_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $goodsReceived->product->product_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($goodsReceived->received_quantity) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($goodsReceived->actual_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($goodsReceived->actual_price * $goodsReceived->received_quantity, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                Total Value:
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">
                                Rp {{ number_format($goodsReceived->actual_price * $goodsReceived->received_quantity, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
