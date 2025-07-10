@extends('layouts.sidebar')

@section('title', 'Goods Received Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Goods Received #{{ $goodsReceived->id }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('goods-received.edit', $goodsReceived) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
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
                        <dd class="mt-1 text-sm text-gray-900">#{{ $goodsReceived->purchase_order_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->supplier }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Received Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->received_at ? $goodsReceived->received_at->format('M d, Y') : 'Not Set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Invoice Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->invoice_number ?: 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Value</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($goodsReceived->total_value, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Received By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $goodsReceived->receiver->name }}</dd>
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
            <h3 class="text-lg font-medium text-gray-900 mb-4">Received Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity Received
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Condition
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Value
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($goodsReceived->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity_received }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($item->condition === 'good') bg-green-100 text-green-800
                                        @elseif($item->condition === 'damaged') bg-red-100 text-red-800
                                        @elseif($item->condition === 'partial') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($item->condition) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($item->total_value, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                Total Value:
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">
                                ${{ number_format($goodsReceived->total_value, 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
