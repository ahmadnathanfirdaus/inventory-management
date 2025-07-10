@extends('layouts.sidebar')

@section('title', 'Order Approval Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Order Approval #{{ $order->id }}</h1>
            <a href="{{ route('approvals.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Approvals
            </a>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'approved') bg-green-100 text-green-800
                                @elseif($order->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->description }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">Rp {{ number_format($order->getTotalAmount(), 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->admin?->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($order->rejection_note)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Rejection Note</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->rejection_note }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->orderRequestItems ?? [] as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->item_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                Total Amount:
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">
                                Rp {{ number_format($order->getTotalAmount(), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($order->status === 'pending')
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Action Required
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Please review this order and take appropriate action.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="button"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium"
                    onclick="openRejectModal()">
                <i class="fas fa-times mr-2"></i>Reject Order
            </button>
            <form action="{{ route('approvals.approve', $order) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium"
                        onclick="return confirm('Are you sure you want to approve this order?')">
                    <i class="fas fa-check mr-2"></i>Approve Order
                </button>
            </form>
        </div>
        @elseif($order->status === 'approved')
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">
                        Order Approved
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>This order has been approved and is ready for processing.</p>
                    </div>
                </div>
            </div>
        </div>
        @elseif($order->status === 'rejected')
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Order Rejected
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>This order has been rejected and cannot be processed.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Order</h3>
            <form action="{{ route('approvals.reject', $order) }}" method="POST" id="rejectForm">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Rejection <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Please provide a detailed reason for rejecting this order..."
                            required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md"
                            onclick="closeRejectModal()">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                        Reject Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

@endsection
