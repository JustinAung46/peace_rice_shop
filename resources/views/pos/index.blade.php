@extends('layouts.app')

@section('content')
<div class="flex h-[calc(100vh-8rem)]">
    <!-- Product Grid (Left) -->
    <div class="w-2/3 pr-6 overflow-y-auto">
        <div class="mb-4">
            <input type="text" id="search" placeholder="Search products..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm">
        </div>

        <div class="grid grid-cols-3 gap-4" id="product-grid">
            @foreach($products as $product)
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 cursor-pointer hover:border-indigo-500 transition-all product-card"
                 onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->current_selling_price }}, {{ $product->stock_count }})">
                <div class="h-24 bg-slate-100 rounded-lg mb-3 flex items-center justify-center text-slate-400">
                    @if($product->image_path)
                        <img src="{{ $product->image_path }}" class="h-full w-full object-cover rounded-lg">
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    @endif
                </div>
                <h3 class="font-semibold text-slate-800 text-sm mb-1 truncate">{{ $product->name }}</h3>
                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-indigo-600 font-bold">{{ number_format($product->current_selling_price) }} K <span class="text-xs text-slate-400 font-normal">/bag</span></span>
                        @if($product->price_per_pyi)
                            <span class="text-slate-500 text-xs">{{ number_format($product->price_per_pyi) }} K /pyi</span>
                        @endif
                    </div>
                    <span class="text-xs {{ $product->stock_count > 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-full">
                        {{ $product->stock_count + 0 }} Left
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Cart (Right) -->
    <div class="w-1/3 bg-white rounded-xl shadow-lg border border-slate-200 flex flex-col h-full">
        <div class="p-4 border-b border-slate-100 bg-slate-50 rounded-t-xl">
            <h2 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Current Order
            </h2>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cart-items">
            <!-- Cart items injected here via JS -->
            <div id="empty-cart-msg" class="text-center text-slate-400 py-10">
                Cart is empty
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 rounded-b-xl">
            <div class="flex justify-between items-center mb-2">
                <span class="text-slate-600">Subtotal</span>
                <span class="font-bold text-slate-800" id="cart-total">0 MMK</span>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-4">
                <button onclick="setPayment('Cash')" id="btn-cash" class="py-2 px-3 border border-indigo-200 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 ring-2 ring-indigo-500">Cash</button>
                <button onclick="setPayment('Mobile')" id="btn-mobile" class="py-2 px-3 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100">Mobile Pay</button>
            </div>

            <button onclick="processCheckout()" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed" id="checkout-btn" disabled>
                Checkout <span id="checkout-total">(0 MMK)</span>
            </button>
        </div>
    </div>
</div>

<script>
    let cart = {};
    let currentPaymentMethod = 'Cash';

    function addToCart(id, name, price, maxStock) {
        if (maxStock <= 0) {
            alert('Out of stock!');
            return;
        }

        if (!cart[id]) {
            cart[id] = { id, name, price, quantity: 0, maxStock };
        }

        if (cart[id].quantity < maxStock) {
            cart[id].quantity++;
            renderCart();
        } else {
            alert('Max stock reached!');
        }
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

    function renderCart() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('empty-cart-msg');
        container.innerHTML = '';
        
        let total = 0;
        const items = Object.values(cart);

        if (items.length === 0) {
            container.appendChild(emptyMsg);
            document.getElementById('checkout-btn').disabled = true;
        } else {
            items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-white p-3 rounded-lg border border-slate-100 shadow-sm';
                div.innerHTML = `
                    <div class="flex-1">
                        <div class="font-medium text-slate-800 text-sm">${item.name}</div>
                        <div class="text-xs text-slate-500">${item.price.toLocaleString()} x ${item.quantity}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQty(${item.id}, -1)" class="w-6 h-6 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center">-</button>
                        <span class="text-sm font-semibold w-4 text-center">${item.quantity}</span>
                        <button onclick="updateQty(${item.id}, 1)" class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 hover:bg-indigo-200 flex items-center justify-center">+</button>
                    </div>
                `;
                container.appendChild(div);
            });
            document.getElementById('checkout-btn').disabled = false;
        }

        document.getElementById('cart-total').innerText = total.toLocaleString() + ' MMK';
        document.getElementById('checkout-total').innerText = '(' + total.toLocaleString() + ' MMK)';
    }

    function setPayment(method) {
        currentPaymentMethod = method;
        document.getElementById('btn-cash').className = method === 'Cash' 
            ? 'py-2 px-3 border border-indigo-200 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 ring-2 ring-indigo-500' 
            : 'py-2 px-3 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100';
            
        document.getElementById('btn-mobile').className = method === 'Mobile' 
            ? 'py-2 px-3 border border-indigo-200 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 ring-2 ring-indigo-500' 
            : 'py-2 px-3 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100';
    }

    async function processCheckout() {
        if (Object.keys(cart).length === 0) return;

        const btn = document.getElementById('checkout-btn');
        btn.disabled = true;
        btn.innerText = 'Processing...';

        try {
            const response = await fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cart: Object.values(cart),
                    payment_method: currentPaymentMethod
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('Sale Successful! Invoice: ' + result.invoice);
                cart = {};
                renderCart();
                window.location.reload(); // Reload to update stock numbers
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('System Error');
            console.error(error);
        } finally {
            btn.disabled = false;
            renderCart(); // Reset button text
        }
    }

    // Simple Search Filter
    document.getElementById('search').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.querySelector('h3').innerText.toLowerCase();
            card.style.display = name.includes(term) ? 'block' : 'none';
        });
    });
</script>
@endsection
