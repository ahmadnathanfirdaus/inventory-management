@extends('layouts.sidebar')

@section('title', 'Point of Sale')

@section('content')
<div class="py-6" x-data="posSystem()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Point of Sale</h1>
            <p class="text-gray-600">Scan or search products to add them to the cart</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side - Product Search & Grid -->
            <div class="lg:col-span-2">
                <!-- Search Bar -->
                <div class="bg-white shadow rounded-lg p-4 mb-6">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <input type="text"
                                   x-model="searchQuery"
                                   @input="searchProducts"
                                   placeholder="Search products by name, SKU, or barcode..."
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <input type="text"
                                   x-model="barcodeInput"
                                   @keyup.enter="searchByBarcode"
                                   placeholder="Scan barcode..."
                                   class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="bg-white shadow rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Products</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" x-show="products.length > 0">
                        <template x-for="product in products" :key="product.id">
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 cursor-pointer transition-colors"
                                 @click="addToCart(product)">
                                <div class="aspect-w-1 aspect-h-1 mb-3">
                                    <img :src="product.image || '/images/default-product.png'"
                                         :alt="product.name"
                                         class="w-full h-32 object-cover rounded-md">
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 mb-1" x-text="product.name"></h4>
                                <p class="text-xs text-gray-500 mb-2" x-text="product.brand?.name || 'No Brand'"></p>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-blue-600" x-text="'Rp ' + formatNumber(product.sale_price)"></span>
                                    <span class="text-xs text-gray-500" x-text="'Stock: ' + product.stock_quantity"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="products.length === 0" class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p>Search for products to display them here</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Shopping Cart -->
            <div class="bg-white shadow rounded-lg p-4 h-fit">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Shopping Cart</h3>

                <!-- Cart Items -->
                <div class="space-y-3 mb-4" x-show="cart.length > 0">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900" x-text="item.name"></h4>
                                <p class="text-xs text-gray-500" x-text="'Rp ' + formatNumber(item.price)"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="decreaseQuantity(index)"
                                        class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span class="text-sm font-medium w-8 text-center" x-text="item.quantity"></span>
                                <button @click="increaseQuantity(index)"
                                        class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                <button @click="removeFromCart(index)"
                                        class="w-6 h-6 rounded-full bg-red-200 flex items-center justify-center text-red-600 hover:bg-red-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="cart.length === 0" class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L17 18"></path>
                    </svg>
                    <p>Cart is empty</p>
                </div>

                <!-- Cart Summary -->
                <div x-show="cart.length > 0" class="border-t border-gray-200 pt-4">
                    <!-- Customer Info -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name (Optional)</label>
                        <input type="text"
                               x-model="customerName"
                               placeholder="Walk-in Customer"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <!-- Discount -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount</label>
                        <div class="flex space-x-2">
                            <input type="number"
                                   x-model="discountAmount"
                                   @input="calculateTotal"
                                   placeholder="0"
                                   min="0"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <select x-model="discountType" @change="calculateTotal" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="amount">Rp</option>
                                <option value="percentage">%</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tax -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax (%)</label>
                        <input type="number"
                               x-model="taxPercentage"
                               @input="calculateTotal"
                               placeholder="0"
                               min="0"
                               max="100"
                               step="0.1"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <!-- Totals -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span x-text="'Rp ' + formatNumber(subtotal)"></span>
                        </div>
                        <div class="flex justify-between" x-show="discountAmount > 0">
                            <span>Discount:</span>
                            <span x-text="'- Rp ' + formatNumber(finalDiscountAmount)"></span>
                        </div>
                        <div class="flex justify-between" x-show="taxPercentage > 0">
                            <span>Tax:</span>
                            <span x-text="'Rp ' + formatNumber(finalTaxAmount)"></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                            <span>Total:</span>
                            <span x-text="'Rp ' + formatNumber(total)"></span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select x-model="paymentMethod" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-2">
                        <button @click="processPayment"
                                :disabled="cart.length === 0 || processing"
                                class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!processing">Complete Sale</span>
                            <span x-show="processing">Processing...</span>
                        </button>

                        <button @click="clearCart"
                                class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function posSystem() {
    return {
        searchQuery: '',
        barcodeInput: '',
        products: [],
        cart: [],
        customerName: '',
        discountAmount: 0,
        discountType: 'amount',
        taxPercentage: 0,
        paymentMethod: 'cash',
        processing: false,

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get finalDiscountAmount() {
            if (this.discountType === 'percentage') {
                return this.subtotal * (this.discountAmount / 100);
            }
            return this.discountAmount;
        },

        get finalTaxAmount() {
            const afterDiscount = this.subtotal - this.finalDiscountAmount;
            return afterDiscount * (this.taxPercentage / 100);
        },

        get total() {
            return this.subtotal - this.finalDiscountAmount + this.finalTaxAmount;
        },

        formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },

        async searchProducts() {
            if (this.searchQuery.length < 2) {
                this.products = [];
                return;
            }

            try {
                const response = await fetch(`/api/products/search?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                this.products = data;
            } catch (error) {
                console.error('Error searching products:', error);
            }
        },

        async searchByBarcode() {
            if (!this.barcodeInput) return;

            try {
                const response = await fetch(`/api/products/by-barcode/${encodeURIComponent(this.barcodeInput)}`);
                const data = await response.json();

                if (data.success) {
                    this.addToCart(data.product);
                    this.barcodeInput = '';
                } else {
                    alert('Product not found with barcode: ' + this.barcodeInput);
                }
            } catch (error) {
                console.error('Error searching by barcode:', error);
                alert('Error searching for product');
            }
        },

        addToCart(product) {
            if (product.stock_quantity <= 0) {
                alert('This product is out of stock');
                return;
            }

            const existingIndex = this.cart.findIndex(item => item.id === product.id);

            if (existingIndex !== -1) {
                if (this.cart[existingIndex].quantity < product.stock_quantity) {
                    this.cart[existingIndex].quantity++;
                } else {
                    alert('Insufficient stock');
                }
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.sale_price,
                    quantity: 1,
                    max_quantity: product.stock_quantity
                });
            }

            this.calculateTotal();
        },

        increaseQuantity(index) {
            if (this.cart[index].quantity < this.cart[index].max_quantity) {
                this.cart[index].quantity++;
                this.calculateTotal();
            } else {
                alert('Insufficient stock');
            }
        },

        decreaseQuantity(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
                this.calculateTotal();
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.calculateTotal();
        },

        clearCart() {
            this.cart = [];
            this.customerName = '';
            this.discountAmount = 0;
            this.taxPercentage = 0;
            this.calculateTotal();
        },

        calculateTotal() {
            // Reactive properties will auto-calculate
        },

        async processPayment() {
            if (this.cart.length === 0) return;

            this.processing = true;

            const saleData = {
                customer_name: this.customerName || 'Walk-in Customer',
                items: this.cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    unit_price: item.price
                })),
                subtotal: this.subtotal,
                discount_amount: this.finalDiscountAmount,
                tax_amount: this.finalTaxAmount,
                total_amount: this.total,
                payment_method: this.paymentMethod
            };

            try {
                const response = await fetch('/pos/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(saleData)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Sale completed successfully!');
                    // Open receipt in new window
                    window.open(`/pos/${result.sale_id}/receipt`, '_blank');
                    this.clearCart();
                } else {
                    alert('Error processing sale: ' + result.message);
                }
            } catch (error) {
                console.error('Error processing payment:', error);
                alert('Error processing payment');
            } finally {
                this.processing = false;
            }
        }
    }
}
</script>
@endsection
