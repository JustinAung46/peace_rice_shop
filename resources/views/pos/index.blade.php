@extends('layouts.app')

@section('content')
<div class="flex h-[calc(100vh-8rem)] gap-6">
    <!-- Categories Sidebar (Left) -->
    <div class="w-48 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100">
            <h3 class="font-bold text-slate-700">Categories</h3>
        </div>
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            <button onclick="filterCategory('all')" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors bg-indigo-50 text-indigo-700 hover:bg-indigo-100 category-btn" data-id="all">
                All Products
            </button>
            @foreach($categories as $category)
            <button onclick="filterCategory({{ $category->id }})" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors category-btn" data-id="{{ $category->id }}">
                {{ $category->name }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- Product Grid (Middle) -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Search & Customer Select -->
        <div class="mb-4 flex gap-4">
            <div class="flex-1 relative">
                <input type="text" id="search" placeholder="Search products..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="w-1/3">
                <select id="customer-select" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm bg-white" onchange="updatePaymentOptions()">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" data-credit="{{ $customer->credit_balance }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Grid -->
        <div class="flex-1 overflow-y-auto pr-2">
            <div class="grid grid-cols-3 gap-4" id="product-grid">
                @foreach($products as $product)
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:border-indigo-500 transition-all product-card group"
                     data-category="{{ $product->category_id ?? 'uncategorized' }}"
                     data-name="{{ strtolower($product->name) }}"
                     onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->current_selling_price }}, {{ $product->stock_count }})">
                    
                    <div class="h-24 bg-slate-50 rounded-lg mb-3 flex items-center justify-center text-slate-300 group-hover:bg-slate-100 transition-colors">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" class="h-full w-full object-cover rounded-lg">
                        @else
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        @endif
                    </div>
                    
                    <h3 class="font-semibold text-slate-800 text-sm mb-1 truncate">{{ $product->name }}</h3>
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-indigo-600 font-bold">{{ number_format($product->current_selling_price) }}</span>
                            <span class="text-xs text-slate-400">MMK / bag</span>
                            @if($product->price_per_pyi)
                                <span class="text-xs text-slate-500 mt-0.5">{{ number_format($product->price_per_pyi) }} K/pyi</span>
                            @endif
                        </div>
                        <span class="text-xs {{ $product->stock_count > 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-full border {{ $product->stock_count > 0 ? 'border-emerald-100' : 'border-red-100' }}">
                            {{ $product->stock_count + 0 }} Left
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="no-products" class="hidden text-center py-10 text-slate-500">No products found</div>
        </div>
    </div>

    <!-- Cart (Right) -->
    <div class="w-96 bg-white rounded-xl shadow-lg border border-slate-200 flex flex-col overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Current Order
            </h2>
            <button onclick="clearCart()" class="text-xs text-red-500 hover:text-red-700 font-medium">Clear</button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cart-items">
            <div id="empty-cart-msg" class="text-center text-slate-400 py-10 flex flex-col items-center">
                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Cart is empty</span>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200">
            <div class="space-y-2 mb-4 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span id="cart-subtotal" class="font-medium">0 MMK</span>
                </div>
                <div class="flex justify-between text-teal-600">
                    <span>Discount</span>
                    <span id="cart-discount" class="font-medium">-0 MMK</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-slate-800 pt-2 border-t border-slate-200">
                    <span>Total</span>
                    <span id="cart-total">0 MMK</span>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button onclick="setPayment('Cash')" id="btn-cash" class="payment-btn py-2 px-1 border rounded-lg text-xs font-medium text-center transition-all bg-indigo-50 border-indigo-200 text-indigo-700 ring-2 ring-indigo-500">Cash</button>
                <button onclick="setPayment('Kpay')" id="btn-kpay" class="payment-btn py-2 px-1 border rounded-lg text-xs font-medium text-center transition-all border-slate-200 text-slate-600 hover:bg-slate-100">Kpay</button>
                <button onclick="setPayment('Credit')" id="btn-credit" class="payment-btn py-2 px-1 border rounded-lg text-xs font-medium text-center transition-all border-slate-200 text-slate-400 cursor-not-allowed" disabled title="Select a customer first">Credit</button>
            </div>

            <button onclick="processCheckout()" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" id="checkout-btn" disabled>
                Checkout
            </button>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-100 origin-center">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex justify-between">
            <span id="modal-item-name">Edit Item</span>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </h3>
        
        <input type="hidden" id="modal-item-id">
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Unit Price (MMK)</label>
                <input type="number" id="modal-price" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Quantity</label>
                    <input type="number" id="modal-qty" step="0.5" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Discount (Total)</label>
                    <input type="number" id="modal-discount" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeModal()" class="px-4 py-2 text-slate-600 hover:text-slate-900 font-medium">Cancel</button>
            <button onclick="saveModal()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Save Changes</button>
        </div>
    </div>
</div>

<script>
    let cart = {};
    let currentPaymentMethod = 'Cash';

    // --- Product Filtering ---
    const searchInput = document.getElementById('search');
    const productGrid = document.getElementById('product-grid');
    const productCards = document.querySelectorAll('.product-card');
    const noProductsMsg = document.getElementById('no-products');
    const categoryBtns = document.querySelectorAll('.category-btn');
    let activeCategory = 'all';

    function filterProducts() {
        const term = searchInput.value.toLowerCase();
        let visibleCount = 0;

        productCards.forEach(card => {
            const name = card.dataset.name;
            const category = card.dataset.category;
            const matchesSearch = name.includes(term);
            const matchesCategory = activeCategory === 'all' || category == activeCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noProductsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        productGrid.style.display = visibleCount === 0 ? 'none' : 'grid';
    }

    searchInput.addEventListener('input', filterProducts);

    function filterCategory(id) {
        activeCategory = id;
        
        // Update styling
        categoryBtns.forEach(btn => {
            if(btn.dataset.id == id) {
                btn.className = 'w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors bg-indigo-50 text-indigo-700 hover:bg-indigo-100 category-btn';
            } else {
                btn.className = 'w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors category-btn';
            }
        });

        filterProducts();
    }

    // --- Cart Logic ---

    function addToCart(id, name, price, maxStock) {
        if (maxStock <= 0) {
            alert('Out of stock!');
            return;
        }

        if (!cart[id]) {
            cart[id] = { 
                id, 
                name, 
                price: parseFloat(price), 
                quantity: 0, 
                maxStock, 
                discount: 0 
            };
        }

        if (cart[id].quantity < maxStock) {
            cart[id].quantity++;
            renderCart();
        } else {
            alert('Max stock reached!');
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('empty-cart-msg');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        container.innerHTML = '';
        
        let subtotal = 0;
        let totalDiscount = 0;
        const items = Object.values(cart);

        if (items.length === 0) {
            container.appendChild(emptyMsg);
            checkoutBtn.disabled = true;
        } else {
            items.forEach(item => {
                const itemSubtotal = item.price * item.quantity;
                subtotal += itemSubtotal;
                totalDiscount += parseFloat(item.discount || 0);

                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-white p-3 rounded-lg border border-slate-100 shadow-sm group hover:border-indigo-300 transition-colors cursor-pointer';
                div.onclick = (e) => {
                    if(!e.target.closest('button')) openModal(item.id);
                };
                
                div.innerHTML = `
                    <div class="flex-1 min-w-0 mr-3">
                        <div class="font-medium text-slate-800 text-sm truncate">${item.name}</div>
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                             <span>${parseInt(item.price).toLocaleString()} x ${item.quantity}</span>
                             ${item.discount > 0 ? `<span class="text-red-500 bg-red-50 px-1 rounded">-${parseInt(item.discount).toLocaleString()}</span>` : ''}
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button onclick="updateQty(${item.id}, -1)" class="w-6 h-6 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-lg leading-none pb-1">-</button>
                        <span class="text-sm font-semibold w-6 text-center text-slate-700">${item.quantity}</span>
                        <button onclick="updateQty(${item.id}, 1)" class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 hover:bg-indigo-200 flex items-center justify-center text-lg leading-none pb-1">+</button>
                    </div>
                `;
                container.appendChild(div);
            });
            checkoutBtn.disabled = false;
        }

        const netTotal = subtotal - totalDiscount;
        
        document.getElementById('cart-subtotal').innerText = subtotal.toLocaleString() + ' MMK';
        document.getElementById('cart-discount').innerText = totalDiscount > 0 ? '-' + totalDiscount.toLocaleString() + ' MMK' : '0 MMK';
        document.getElementById('cart-total').innerText = netTotal.toLocaleString() + ' MMK';
    }

    function updateQty(id, change) {
        if (cart[id]) {
            const newQty = cart[id].quantity + change;
            if (newQty > 0 && newQty <= cart[id].maxStock) {
                cart[id].quantity = newQty;
            } else if (newQty <= 0) {
                delete cart[id];
            }
            renderCart();
        }
    }

    function clearCart() {
        if(confirm('Clear cart?')) {
            cart = {};
            renderCart();
        }
    }

    // --- Modal Logic ---
    const modal = document.getElementById('edit-modal');
    
    function openModal(id) {
        const item = cart[id];
        if(!item) return;

        document.getElementById('modal-item-id').value = id;
        document.getElementById('modal-item-name').innerText = item.name;
        document.getElementById('modal-price').value = item.price;
        document.getElementById('modal-qty').value = item.quantity;
        document.getElementById('modal-discount').value = item.discount;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function saveModal() {
        const id = document.getElementById('modal-item-id').value;
        const price = parseFloat(document.getElementById('modal-price').value);
        const qty = parseFloat(document.getElementById('modal-qty').value);
        const discount = parseFloat(document.getElementById('modal-discount').value);

        if (cart[id]) {
             if (qty <= 0) {
                delete cart[id];
            } else {
                 if(qty > cart[id].maxStock) {
                     alert('Exceeds stock!');
                     return; 
                 }
                cart[id].price = price;
                cart[id].quantity = qty;
                cart[id].discount = discount;
            }
            renderCart();
            closeModal();
        }
    }

    // --- Payment Logic ---
    function updatePaymentOptions() {
        const customerId = document.getElementById('customer-select').value;
        const creditBtn = document.getElementById('btn-credit');
        
        if (customerId) {
            creditBtn.disabled = false;
            creditBtn.classList.remove('text-slate-400', 'cursor-not-allowed', 'border-slate-200');
            creditBtn.classList.add('text-slate-600', 'hover:bg-slate-100', 'border-slate-200'); // Default state
            creditBtn.removeAttribute('title');
        } else {
            creditBtn.disabled = true;
            creditBtn.classList.add('text-slate-400', 'cursor-not-allowed', 'border-slate-200');
            creditBtn.classList.remove('bg-indigo-50', 'text-indigo-700', 'ring-2', 'ring-indigo-500', 'text-slate-600', 'hover:bg-slate-100');
            creditBtn.title = "Select a customer first";
            
            // If credit was selected, switch back to cash
            if(currentPaymentMethod === 'Credit') {
                setPayment('Cash');
            }
        }
    }

    function setPayment(method) {
        if(method === 'Credit' && document.getElementById('btn-credit').disabled) return;

        currentPaymentMethod = method;
        
        document.querySelectorAll('.payment-btn').forEach(btn => {
            btn.className = 'payment-btn py-2 px-1 border rounded-lg text-xs font-medium text-center transition-all border-slate-200 text-slate-600 hover:bg-slate-100';
        });

        const activeBtn = document.getElementById('btn-' + method.toLowerCase());
        activeBtn.className = 'payment-btn py-2 px-1 border rounded-lg text-xs font-medium text-center transition-all bg-indigo-50 border-indigo-200 text-indigo-700 ring-2 ring-indigo-500';
    }

    // --- Checkout ---
    async function processCheckout() {
        if (Object.keys(cart).length === 0) return;

        const customerId = document.getElementById('customer-select').value;
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.disabled = true;
        checkoutBtn.innerText = 'Checking Stock...';

        const payload = {
            cart: Object.values(cart).map(item => ({
                id: item.id,
                quantity: item.quantity,
                unit_price: item.price,
                discount: item.discount
            })),
            payment_method: currentPaymentMethod,
            customer_id: customerId || null
    };

        try {
            // ✅ STEP 1 — CHECK STOCK FIRST
            const checkResponse = await fetch('{{ route("pos.checkStock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const checkResult = await checkResponse.json();
            // ✅ IF INSUFFICIENT
            if (checkResult.status === 'insufficient') {
                const transferResult = await confirmStockTransfer(checkResult);

                // Check if user cancelled or transfer failed
                if (!transferResult.transferred) {
                    
                    // Show appropriate message
                    if (transferResult.cancelled) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cancelled',
                            text: 'Checkout cancelled due to insufficient stock.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        cart = {};
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Transfer Failed',
                            text: transferResult.error || 'Failed to transfer stock. Please try again.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    
                    checkoutBtn.disabled = false;
                    checkoutBtn.innerText = 'Checkout';
                    return;
                }

            }

            // ✅ STEP 2 — PROCESS SALE
            checkoutBtn.innerText = 'Processing...';
            const response = await fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sale Successful!',
                    text: 'Invoice: ' + result.invoice,
                    confirmButtonText: 'OK'
                }).then(() => {
                    cart = {};
                    window.location.reload();
                });
            } else
            {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('System Error');
            console.error(error);
        } finally {
            checkoutBtn.disabled = false;
            checkoutBtn.innerText = 'Checkout';
        }
    }

// Compact confirmation function
async function confirmStockTransfer(checkResult) {
    const itemsList = checkResult.items.map(item => 
        `• ${item.product_name}: ${item.needed} units (${item.from_warehouse})`
    ).join('<br>');

    const result = await Swal.fire({
        title: '⚠️ Insufficient Stock',
        html: `
            <div style="text-align: left;">
                <p style="color: #dc3545; font-weight: bold;">Missing stock for:</p>
                <p style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    ${itemsList}
                </p>
                <p style="margin-top: 15px;">Transfer stock and continue?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, transfer',
        cancelButtonText: 'Cancel'
    });

    if (!result.isConfirmed) {
        return { transferred: false, cancelled: true };
    }

    try {
        // Show loading state
        Swal.fire({
            title: 'Transferring Stock',
            html: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ===========================================
        // CORRECT PAYLOAD FOR YOUR SERVICE
        // ===========================================
        
        // Since service processes ONE product at a time,
        // we need to handle each insufficient item separately
        
        const transferPromises = checkResult.items.map(async (item) => {
            alert(item)
            const payload = {
                product_id: item.product_id,           // $productId
                from_warehouse_id: item.from_warehouse_id, // $fromId  
                to_warehouse_id: 1,                    // $toId (Shop 1 - target warehouse)
                quantity: item.needed                  // $quantityToTransfer
            };

            const response = await fetch('{{ route("pos.transferStock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(`Failed to transfer ${item.product_name}: ${error.message}`);
            }

            return await response.json();
        });

        // Wait for all transfers to complete
        const transferResults = await Promise.all(transferPromises);

        // Close loading
        Swal.close();

        // Show success
        await Swal.fire({
            icon: 'success',
            title: 'Stock Transferred!',
            text: 'All items have been transferred successfully.',
            timer: 2000,
            showConfirmButton: false
        });

        return { 
            transferred: true, 
            cancelled: false,
            results: transferResults 
        };

    } catch (error) {
        Swal.close();
        
        await Swal.fire({
            icon: 'error',
            title: 'Transfer Failed',
            text: error.message || 'Failed to transfer stock',
            confirmButtonColor: '#dc3545'
        });

        return { 
            transferred: false, 
            cancelled: false, 
            error: error.message 
        };
    }
}
</script>
@endsection
