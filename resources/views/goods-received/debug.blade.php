@extends('layouts.sidebar')

@section('title', 'Debug Goods Receipt')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Debug Goods Receipt</h3>

                <form id="debugForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="order_code" class="block text-sm font-medium text-gray-700">Order Code</label>
                        <input type="text" id="order_code" name="order_code" placeholder="Masukkan order code"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <button type="button" onclick="debugOrder()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Debug Order
                    </button>
                </form>

                <div id="debugResult" class="mt-6 hidden">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Debug Result:</h4>
                    <pre id="debugData" class="bg-gray-100 p-4 rounded-md text-sm overflow-auto"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function debugOrder() {
    const orderCode = document.getElementById('order_code').value;
    if (!orderCode) {
        alert('Masukkan order code terlebih dahulu');
        return;
    }

    try {
        const response = await fetch('/api/goods-received/get-order-details', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_code: orderCode
            })
        });

        const data = await response.json();

        document.getElementById('debugResult').classList.remove('hidden');
        document.getElementById('debugData').textContent = JSON.stringify(data, null, 2);

        if (data.error) {
            document.getElementById('debugData').classList.add('text-red-600');
        } else {
            document.getElementById('debugData').classList.remove('text-red-600');
        }

    } catch (error) {
        document.getElementById('debugResult').classList.remove('hidden');
        document.getElementById('debugData').textContent = 'Error: ' + error.message;
        document.getElementById('debugData').classList.add('text-red-600');
    }
}
</script>
@endsection
