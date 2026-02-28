/**
 * POS Logic
 */

let cart = {};
let payments = []; // Array of {method, amount}
let currentEditingPayment = null; // {index, method} or null
let numpadValue = '0';

const warehouses = window.POS_CONFIG ? window.POS_CONFIG.warehouses : {};

// --- Product Filtering ---
function filterProducts() {
    const searchInput = document.getElementById('search');
    const productGrid = document.getElementById('product-grid');
    const productCards = document.querySelectorAll('.product-card');
    const noProductsMsg = document.getElementById('no-products');
    
    const term = searchInput.value.toLowerCase();
    let visibleCount = 0;

    productCards.forEach(card => {
        const name = card.dataset.name;
        const category = card.dataset.category;
        const matchesSearch = name.includes(term);
        const matchesCategory = window.activeCategory === 'all' || category == window.activeCategory;

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

function filterCategory(id) {
    window.activeCategory = id;
    const categoryBtns = document.querySelectorAll('.category-btn');

    categoryBtns.forEach(btn => {
        if(btn.dataset.id == id) {
            btn.className = 'w-full text-left px-4 py-3 rounded-xl text-base font-bold transition-colors bg-indigo-50 text-indigo-700 hover:bg-indigo-100 shadow-sm border border-indigo-100 category-btn';
        } else {
            btn.className = 'w-full text-left px-4 py-3 rounded-xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent hover:border-slate-100 transition-colors category-btn';
        }
    });

    filterProducts();
}

// ─── Variant Picker State ─────────────────────────────────────────────────
let pickerProductId   = null;
let pickerProductName = null;
let pickerVariants    = [];
let pickerSelectedId  = null;
let pickerQty         = 1;
let pickerPriceMode   = 'wholesale'; 

function setPriceMode(mode) {
    pickerPriceMode = mode;
    pickerSelectedId = null;
    pickerQty = 1;

    const wholesaleBtn = document.getElementById('price-mode-wholesale');
    const retailBtn = document.getElementById('price-mode-retail');

    if (mode === 'wholesale') {
        wholesaleBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all bg-white text-indigo-700 shadow-sm border border-indigo-100';
        retailBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all text-slate-600 hover:bg-slate-50';
    } else {
        retailBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all bg-white text-indigo-700 shadow-sm border border-indigo-100';
        wholesaleBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all text-slate-600 hover:bg-slate-50';
    }

    document.getElementById('picker-qty-display').innerText = '1';
    document.getElementById('picker-stock-label').classList.add('hidden');
    document.getElementById('picker-warehouse-breakdown').classList.add('hidden');
    document.getElementById('picker-add-btn').disabled = true;
    document.getElementById('picker-add-btn').innerHTML = `
        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add to Cart
    `;
    document.getElementById('picker-validation-msg').style.opacity = '0';
    pickerUpdateTotal();
    renderPickerVariants();
}

function renderPickerVariants() {
    const pillsContainer = document.getElementById('variant-picker-pills');
    pillsContainer.innerHTML = '';

    pickerVariants.slice(0, 10).forEach(v => {
        let displayPrice = 0;
        if (pickerPriceMode === 'wholesale') {
            displayPrice = v.selling_price;
        } else {
            if (v.price_per_pyi && v.pyi_per_bag) {
                displayPrice = parseInt(v.price_per_pyi) * parseInt(v.pyi_per_bag);
            } else {
                return;
            }
        }

        const card = document.createElement('button');
        card.type = 'button';
        card.dataset.variantId = v.id;
        const outOfStock = v.stock_count <= 0;

        let cardClasses = 'variant-pill relative w-full px-3 py-3 text-xs md:text-sm font-medium rounded-xl border transition-all duration-200 text-left ';

        if (outOfStock) {
            cardClasses += 'border-slate-200 bg-slate-50 opacity-50 cursor-not-allowed';
        } else {
            cardClasses += 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50 cursor-pointer';
            if (parseInt(pickerSelectedId) === parseInt(v.id)) {
                cardClasses = 'variant-pill relative w-full px-3 py-3 text-xs md:text-sm font-medium rounded-xl border-indigo-600 bg-indigo-50 ring-2 ring-indigo-600/20 shadow-md text-left';
            }
        }

        card.className = cardClasses;
        card.disabled = outOfStock;
        let priceText = parseInt(displayPrice).toLocaleString() + ' MMK';

        card.innerHTML = `
            <div class="flex items-center justify-between mb-1.5">
                <span class="variant-name font-semibold text-slate-800 text-sm md:text-base">${v.name}</span>
                ${outOfStock ? '<span class="text-[10px] font-medium text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded-full">Sold Out</span>' : ''}
            </div>
            <div class="flex items-center justify-between">
                <span class="variant-price text-xs md:text-sm font-bold text-indigo-600">${priceText}</span>
                <span class="select-check w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center ${parseInt(pickerSelectedId) === parseInt(v.id) ? '' : 'scale-0'} transition-transform duration-200">
                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
        `;

        if (!outOfStock) card.onclick = () => pickerSelectVariant(v.id);
        pillsContainer.appendChild(card);
    });

    if (pickerVariants.length > 10) {
        const moreIndicator = document.createElement('div');
        moreIndicator.className = 'col-span-full text-center py-2 text-xs md:text-sm text-slate-400 border-t border-slate-100 mt-2';
        moreIndicator.textContent = `+${pickerVariants.length - 10} more variants available`;
        pillsContainer.appendChild(moreIndicator);
    }
}

function openVariantPicker(productId, productName, variants, imageUrl) {
    pickerProductId   = productId;
    pickerProductName = productName;
    pickerVariants    = variants;
    pickerSelectedId  = null;
    pickerQty         = 1;
    pickerPriceMode   = 'wholesale';

    document.getElementById('variant-picker-title').innerText = productName;
    const img = document.getElementById('picker-img');
    const placeholder = document.getElementById('picker-img-placeholder');
    if (imageUrl) {
        img.src = imageUrl;
        img.classList.remove('hidden');
        placeholder.classList.add('hidden');
    } else {
        img.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }

    setPriceMode('wholesale');
    document.getElementById('picker-qty-display').innerText = '1';
    document.getElementById('picker-total-display').innerHTML = `0 <span class="text-sm font-bold text-slate-400">MMK</span>`;
    document.getElementById('picker-stock-label').classList.add('hidden');
    document.getElementById('picker-warehouse-breakdown').classList.add('hidden');
    document.getElementById('picker-add-btn').disabled = true;
    document.getElementById('picker-validation-msg').style.opacity = '0';

    const modal = document.getElementById('variant-picker-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function pickerSelectVariant(variantId) {
    pickerSelectedId = variantId;
    pickerQty        = 1;
    renderPickerVariants();

    const v = pickerVariants.find(x => x.id === variantId);
    if (!v) return;

    const stockLabel = document.getElementById('picker-stock-label');
    stockLabel.innerHTML = `<span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>${v.stock_count} ${v.unit_label} total</span>`;
    stockLabel.classList.remove('hidden');

    const breakdownEl = document.getElementById('picker-warehouse-breakdown');
    if (v.stock_batches && v.stock_batches.length > 0) {
        const breakdown = {};
        v.stock_batches.forEach(batch => {
            const wName = batch.warehouse ? batch.warehouse.name : 'Unknown';
            breakdown[wName] = (breakdown[wName] || 0) + parseInt(batch.remaining_quantity);
        });
        
        const breakdownStr = Object.entries(breakdown)
            .map(([name, qty]) => `${qty} in ${name}`)
            .join(', ');
        
        breakdownEl.innerText = breakdownStr;
        breakdownEl.classList.remove('hidden');
    } else {
        breakdownEl.classList.add('hidden');
    }

    document.getElementById('picker-qty-display').innerText = '1';
    document.getElementById('picker-add-btn').disabled = false;
    document.getElementById('picker-add-btn').innerHTML = `<svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add ${v.name}`;
    document.getElementById('picker-validation-msg').style.opacity = '0';
    document.getElementById('picker-validation-msg').style.display = 'none';
    pickerUpdateTotal();
}

function pickerChangeQty(delta) {
    if (!pickerSelectedId) {
        const msg = document.getElementById('picker-validation-msg');
        msg.style.opacity = '1';
        msg.style.display = 'block';
        msg.textContent = 'Please select a variant first';
        const variantSection = document.querySelector('#variant-picker-pills');
        variantSection.classList.add('animate-shake');
        setTimeout(() => variantSection.classList.remove('animate-shake'), 500);
        return;
    }
    const v = pickerVariants.find(x => x.id === pickerSelectedId);
    if (!v) return;

    const newQty = pickerQty + delta;
    if (newQty < 1 || newQty > v.stock_count) {
        const msg = document.getElementById('picker-validation-msg');
        msg.style.opacity = '1';
        msg.style.display = 'block';
        msg.textContent = newQty < 1 ? 'Minimum quantity is 1' : `Maximum ${v.stock_count} available`;
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.display = 'none';
        }, 2000);
        return;
    }
    pickerQty = newQty;
    document.getElementById('picker-qty-display').innerText = pickerQty;
    pickerUpdateTotal();
}

function pickerUpdateTotal() {
    if (!pickerSelectedId) {
        document.getElementById('picker-total-display').innerText = '0 MMK';
        return;
    }
    const v = pickerVariants.find(x => x.id === pickerSelectedId);
    if (!v) return;

    let unitPrice = pickerPriceMode === 'wholesale' ? parseInt(v.selling_price) : parseInt(v.price_per_pyi) * parseInt(v.pyi_per_bag);
    const total = unitPrice * pickerQty;
    document.getElementById('picker-total-display').innerHTML = `${total.toLocaleString()} <span class="text-sm font-bold text-slate-400">MMK</span>`;
}

function pickerAddToCart() {
    if (!pickerSelectedId) return;
    const v = pickerVariants.find(x => x.id === pickerSelectedId);
    if (!v) return;

    let unitPrice = pickerPriceMode === 'wholesale' ? parseInt(v.selling_price) : parseInt(v.price_per_pyi) * parseInt(v.pyi_per_bag);
    let priceLabel = pickerPriceMode === 'wholesale' ? '(Wholesale)' : '(Retail)';

    const warehouseId = parseInt(document.getElementById('session-warehouse').value);
    const key = 'v' + v.id + '_w' + warehouseId;

    if (!cart[key]) {
        cart[key] = { id: key, variant_id: v.id, product_id: pickerProductId, name: pickerProductName + ' – ' + v.name + ' ' + priceLabel, price: unitPrice, quantity: 0, maxStock: v.stock_count, discount: 0, warehouse_id: warehouseId, unit_label: v.unit_label, price_mode: pickerPriceMode };
    } else {
        cart[key].price = unitPrice;
        cart[key].name = pickerProductName + ' – ' + v.name + ' ' + priceLabel;
        cart[key].price_mode = pickerPriceMode;
    }

    const spaceLeft = cart[key].maxStock - cart[key].quantity;
    const toAdd = Math.min(pickerQty, spaceLeft);

    if (toAdd <= 0) {
        Swal.fire({ icon: 'warning', title: 'Limit Reached', text: 'You already have all available stock in your bag.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
        return;
    }

    cart[key].quantity += toAdd;
    renderCart();
    closeVariantPicker();
    Swal.fire({ icon: 'success', title: 'Added to Bag!', text: `${toAdd} × ${v.name} added successfully`, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
}

function closeVariantPicker() {
    const modal = document.getElementById('variant-picker-modal');
    modal.classList.add('opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex', 'opacity-0'); }, 300);
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const checkoutBtn = document.getElementById('checkout-btn');
    container.innerHTML = '';
    let subtotal = 0, totalDiscount = 0;
    const items = Object.values(cart).filter(item => item.quantity > 0);

    if (items.length === 0) {
        container.innerHTML = `<div id="empty-cart-msg" class="text-center text-slate-400 py-10 flex flex-col items-center">
            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span>Cart is empty</span>
        </div>`;
        checkoutBtn.disabled = true;
    } else {
        items.forEach(item => {
            subtotal += item.price * item.quantity;
            totalDiscount += parseInt(item.discount || 0);
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center bg-white p-3 rounded-lg border border-slate-100 shadow-sm group hover:border-indigo-300 transition-colors cursor-pointer';
            div.onclick = (e) => { if(!e.target.closest('button')) openModal(item.id); };
            div.innerHTML = `
                <div class="flex-1 min-w-0 mr-2">
                    <div class="font-bold text-slate-800 text-[13px] leading-tight mb-0.5">
                        <span class="block truncate">${item.name.split(' – ')[0]}</span>
                        <span class="block truncate text-slate-600">${item.name.split(' – ')[1]}</span>
                    </div>
                    <div class="flex items-center gap-2">
                         <span class="text-[11px] text-slate-500 font-medium">${parseInt(item.price).toLocaleString()} x ${item.quantity}</span>
                         ${item.discount > 0 ? `<span class="text-[10px] text-red-600 bg-red-50 px-1 rounded">-${parseInt(item.discount).toLocaleString()}</span>` : ''}
                         <span class="text-[9px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100 font-bold">${warehouses[item.warehouse_id] || 'Unknown'}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-1.5 ml-2">
                    <button onclick="updateQty('${item.id}', -1)" class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-lg border border-slate-200">-</button>
                    <span class="text-xs font-bold w-5 text-center text-slate-800">${item.quantity}</span>
                    <button onclick="updateQty('${item.id}', 1)" class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 flex items-center justify-center text-lg border border-indigo-100">+</button>
                </div>`;
            container.appendChild(div);
        });
        checkoutBtn.disabled = false;
    }
    const netTotal = subtotal - totalDiscount;
    document.getElementById('cart-subtotal').innerText = subtotal.toLocaleString() + ' MMK';
    document.getElementById('cart-discount').innerText = totalDiscount > 0 ? '-' + totalDiscount.toLocaleString() + ' MMK' : '0 MMK';
    document.getElementById('cart-total').innerText = netTotal.toLocaleString() + ' MMK';
    updatePaymentCalculations();
}

function updateQty(key, change) {
    if (cart[key]) {
        const newQty = cart[key].quantity + change;
        if (newQty > 0 && newQty <= cart[key].maxStock) cart[key].quantity = newQty;
        else if (newQty <= 0) delete cart[key];
        renderCart();
    }
}

function clearCart() {
    if(confirm('Clear cart?')) { cart = {}; payments = []; renderCart(); renderPayments(); }
}

// --- Edit Modal Logic ---
let activeEditField = 'price';
let editValues = { price: '0', qty: '0', discount: '0' };

function setActiveInput(field) {
    activeEditField = field;
    ['price', 'qty', 'discount'].forEach(f => {
        const el = document.getElementById('input-btn-' + f);
        if(f === field) {
            el.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-900');
            el.classList.remove('border-slate-200', 'bg-white', 'text-slate-800', 'opacity-50', 'bg-slate-100');
        } else {
            el.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-900');
            el.classList.add('border-slate-200', 'bg-white', 'text-slate-800');
            const customerId = document.getElementById('customer-select').value;
            if (f === 'price' && !customerId) el.classList.add('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
        }
    });
}

function updateEditDisplay() {
    const formatNum = (val) => (!val || isNaN(parseInt(val))) ? '0' : parseInt(val).toLocaleString();
    document.getElementById('input-btn-price').innerText = formatNum(editValues.price);
    document.getElementById('input-btn-qty').innerText = formatNum(editValues.qty);
    document.getElementById('input-btn-discount').innerText = formatNum(editValues.discount);
    document.getElementById('modal-price').value = editValues.price || 0;
    document.getElementById('modal-qty').value = editValues.qty || 0;
    document.getElementById('modal-discount').value = editValues.discount || 0;
}

function appendEditNumpad(val) {
    let current = editValues[activeEditField].toString();
    if (current === '0' && val !== '00' && val !== '000') current = val;
    else if (current === '0' && (val === '00' || val === '000')) current = '0';
    else if (current.length <= 12) current += val;
    editValues[activeEditField] = current;
    updateEditDisplay();
}

function popEditNumpad() {
    let current = editValues[activeEditField].toString();
    editValues[activeEditField] = current.length > 1 ? current.slice(0, -1) : '0';
    updateEditDisplay();
}

function clearEditNumpad() { editValues[activeEditField] = '0'; updateEditDisplay(); }

function openModal(id) {
    const item = cart[id];
    if(!item) return;
    const customerId = document.getElementById('customer-select').value;
    const priceBtn = document.getElementById('input-btn-price');
    if (!customerId) {
        priceBtn.onclick = null;
        priceBtn.classList.add('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
    } else {
        priceBtn.onclick = () => setActiveInput('price');
        priceBtn.classList.remove('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
    }
    document.getElementById('modal-item-id').value = id;
    document.getElementById('modal-item-name').innerText = item.name;
    editValues.price = item.price.toString();
    editValues.qty = item.quantity.toString();
    editValues.discount = (item.discount || 0).toString();
    updateEditDisplay();
    setActiveInput('discount');
    document.getElementById('modal-warehouse').value = item.warehouse_id || 1;
    const modal = document.getElementById('edit-modal');
    modal.classList.remove('hidden'); modal.classList.add('flex');
}

function closeModal() { document.getElementById('edit-modal').classList.add('hidden'); document.getElementById('edit-modal').classList.remove('flex'); }

function saveModal() {
    const oldKey = document.getElementById('modal-item-id').value;
    const price = parseInt(document.getElementById('modal-price').value) || 0;
    const qty = parseInt(document.getElementById('modal-qty').value) || 0;
    const discount = parseInt(document.getElementById('modal-discount').value) || 0;
    const warehouseId = parseInt(document.getElementById('modal-warehouse').value) || 1;

    if (cart[oldKey]) {
        if (qty <= 0) delete cart[oldKey];
        else {
            if(qty > cart[oldKey].maxStock) { alert('Exceeds stock!'); return; }
            const variantId = cart[oldKey].variant_id;
            const newKey = 'v' + variantId + '_w' + warehouseId;
            if (newKey !== oldKey) {
                if (cart[newKey]) {
                    if (cart[newKey].quantity + qty > cart[newKey].maxStock) { alert('Merging would exceed available stock'); return; }
                    cart[newKey].quantity += qty;
                    delete cart[oldKey];
                } else {
                    cart[newKey] = cart[oldKey]; cart[newKey].id = newKey; cart[newKey].price = price; cart[newKey].quantity = qty; cart[newKey].discount = discount; cart[newKey].warehouse_id = warehouseId; delete cart[oldKey];
                }
            } else {
                cart[oldKey].price = price; cart[oldKey].quantity = qty; cart[oldKey].discount = discount; cart[oldKey].warehouse_id = warehouseId;
            }
        }
        renderCart(); closeModal();
    }
}

// --- Payment Logic ---
function updatePaymentOptions(selectedId = null) {
    const customerId = selectedId !== null ? selectedId : document.getElementById('customer-select').value;
    const creditBtn = document.getElementById('btn-credit');
    if (customerId) {
        creditBtn.disabled = false;
        creditBtn.classList.remove('opacity-60', 'cursor-not-allowed');
    } else {
        creditBtn.disabled = true;
        creditBtn.classList.add('opacity-60', 'cursor-not-allowed');
        payments = payments.filter(p => p.method !== 'Credit');
        renderPayments(); updatePaymentCalculations();
    }
}

function addPayment(method) {
    if(method === 'Credit' && document.getElementById('btn-credit').disabled) return;
    const existingIndex = payments.findIndex(p => p.method === method);
    if (existingIndex !== -1) editPayment(existingIndex);
    else { currentEditingPayment = { index: -1, method: method }; openPaymentModal(method); }
}

function removePayment(index) { payments.splice(index, 1); renderPayments(); updatePaymentCalculations(); }

function editPayment(index) {
    const payment = payments[index];
    currentEditingPayment = { index: index, method: payment.method };
    openPaymentModal(payment.method, payment.amount);
}

function openPaymentModal(method, initialAmount = null) {
    const cartTotal = getCartTotal();
    const otherPaid = payments.reduce((sum, p, i) => i === (currentEditingPayment?.index ?? -1) ? sum : sum + p.amount, 0);
    const remaining = Math.max(0, cartTotal - otherPaid);
    document.getElementById('payment-modal-title').innerText = (currentEditingPayment && currentEditingPayment.index !== -1 ? 'Edit ' : 'Add ') + method + ' Payment';
    
    let subtotal = 0, totalDiscount = 0;
    Object.values(cart).filter(item => item.quantity > 0).forEach(item => { subtotal += item.price * item.quantity; totalDiscount += parseInt(item.discount || 0); });

    document.getElementById('pm-subtotal').innerText = subtotal.toLocaleString() + ' MMK';
    document.getElementById('pm-discount').innerText = totalDiscount > 0 ? '-' + totalDiscount.toLocaleString() + ' MMK' : '0 MMK';
    document.getElementById('pm-net-total').innerText = cartTotal.toLocaleString() + ' MMK';
    const container = document.getElementById('pm-other-payments-container');
    if (otherPaid > 0) { container.classList.remove('hidden'); document.getElementById('pm-other-payments').innerText = otherPaid.toLocaleString() + ' MMK'; }
    else container.classList.add('hidden');
    document.getElementById('pm-remaining').innerText = remaining.toLocaleString() + ' MMK';
    numpadValue = (initialAmount !== null ? initialAmount : remaining).toString();
    updateNumpadDisplay();
    document.getElementById('btn-quick-full').innerText = (payments.length === 0 || (payments.length === 1 && currentEditingPayment?.index !== -1)) ? 'Full Amount' : 'Remaining';
    document.getElementById('payment-modal').classList.remove('hidden');
    document.getElementById('payment-modal').classList.add('flex');
}

function closePaymentModal() { document.getElementById('payment-modal').classList.add('hidden'); document.getElementById('payment-modal').classList.remove('flex'); currentEditingPayment = null; }

function updateNumpadDisplay() { document.getElementById('payment-numpad-display').innerText = (parseInt(numpadValue) || 0).toLocaleString(); }

function appendNumpad(num) { if (numpadValue === '0') numpadValue = num.toString(); else numpadValue += num.toString(); updateNumpadDisplay(); }

function appendNumpadQuick(amount) { numpadValue = ( (parseFloat(numpadValue) || 0) + amount ).toString(); updateNumpadDisplay(); }

function setNumpadValue(type) {
    const otherPaid = payments.reduce((sum, p, i) => i === (currentEditingPayment?.index ?? -1) ? sum : sum + p.amount, 0);
    if (type === 'full_remain') numpadValue = Math.max(0, getCartTotal() - otherPaid).toString();
    updateNumpadDisplay();
}

function clearNumpad() { numpadValue = '0'; updateNumpadDisplay(); }

function popNumpad() { numpadValue = numpadValue.length > 1 ? numpadValue.slice(0, -1) : '0'; updateNumpadDisplay(); }

function confirmPaymentModal() {
    let finalAmount = parseInt(numpadValue) || 0;
    const otherPaid = payments.reduce((sum, p, i) => i === (currentEditingPayment?.index ?? -1) ? sum : sum + p.amount, 0);
    const maxAllowed = Math.max(0, getCartTotal() - otherPaid);

    if (finalAmount > maxAllowed) {
         Swal.fire({ icon: 'error', title: 'Invalid Amount', text: 'Amount cannot exceed ' + maxAllowed.toLocaleString() + ' MMK', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
         return;
    }
    if (currentEditingPayment.index === -1) payments.push({ method: currentEditingPayment.method, amount: finalAmount });
    else payments[currentEditingPayment.index].amount = finalAmount;

    renderPayments(); updatePaymentCalculations();
    setTimeout(() => closePaymentModal(), 50);
}

function getCartTotal() {
    let subtotal = 0, totalDiscount = 0;
    Object.values(cart).filter(item => item.quantity > 0).forEach(item => { subtotal += item.price * item.quantity; totalDiscount += parseInt(item.discount || 0); });
    return subtotal - totalDiscount;
}

function renderPayments() {
    const container = document.getElementById('active-payments');
    container.innerHTML = '';
    payments.forEach((payment, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-indigo-300 group';
        div.onclick = (e) => { if(!e.target.closest('button')) editPayment(index); };
        div.innerHTML = `<div class="flex-1"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">${payment.method}</div><div class="text-sm font-bold text-slate-700">${payment.amount.toLocaleString()} MMK</div></div><button onclick="removePayment(${index})" class="text-slate-300 hover:text-red-500 p-1"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>`;
        container.appendChild(div);
    });
}

function updatePaymentCalculations() {
    const cartTotal = getCartTotal();
    const paidTotal = payments.reduce((sum, p) => sum + p.amount, 0);
    const remaining = cartTotal - paidTotal;
    document.getElementById('paid-total').innerText = paidTotal.toLocaleString() + ' MMK';
    const remainingEl = document.getElementById('remaining-amount');
    const remainingLabel = document.getElementById('remaining-label');

    if (remaining > 0) {
        remainingLabel.childNodes[0].nodeValue = 'Remaining: ';
        remainingEl.className = 'text-red-500 font-bold ml-1';
        remainingEl.innerText = Math.abs(remaining).toLocaleString() + ' MMK';
    } else if (remaining < 0) {
        remainingLabel.childNodes[0].nodeValue = 'Change: ';
        remainingEl.className = 'text-emerald-500 font-bold ml-1';
        remainingEl.innerText = Math.abs(remaining).toLocaleString() + ' MMK';
    } else {
        remainingLabel.childNodes[0].nodeValue = 'Balanced ';
        remainingEl.className = 'text-emerald-600 font-bold ml-1';
        remainingEl.innerText = '✓';
    }

    const checkoutBtn = document.getElementById('checkout-btn');
    checkoutBtn.disabled = (remaining > 0) || cartTotal <= 0;
}

// --- Checkout ---
async function processCheckout() {
    if (Object.keys(cart).length === 0) return;
    const checkoutBtn = document.getElementById('checkout-btn');
    checkoutBtn.disabled = true;
    checkoutBtn.innerText = 'Checking Stock...';

    const checkoutSaleType = Object.values(cart).some(i => i.price_mode === 'wholesale') ? 'wholesale' : 'retail';

    const payload = {
        cart: Object.values(cart).filter(i => i.quantity > 0).map(item => ({ variant_id: item.variant_id, quantity: item.quantity, unit_price: item.price, discount: item.discount, warehouse_id: item.warehouse_id })),
        payments: payments.map(p => ({ method: p.method, amount: p.amount })),
        customer_id: document.getElementById('customer-select').value || null,
        sale_type: checkoutSaleType
    };

    try {
        const checkResponse = await fetch(window.POS_CONFIG.routes.checkStock, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.POS_CONFIG.csrf }, body: JSON.stringify(payload) });
        const checkResult = await checkResponse.json();
        if (checkResult.status === 'insufficient') {
            const transferResult = await confirmStockTransfer(checkResult);
            if (!transferResult.transferred) { checkoutBtn.disabled = false; checkoutBtn.innerText = 'Checkout'; return; }
        }
        checkoutBtn.innerText = 'Processing...';
        const response = await fetch(window.POS_CONFIG.routes.store, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.POS_CONFIG.csrf }, body: JSON.stringify(payload) });
        const result = await response.json();
        if (result.success) { Swal.fire({ icon: 'success', title: 'Sale Successful!', text: 'Invoice: ' + result.invoice }).then(() => { cart = {}; window.location.reload(); }); }
        else alert('Error: ' + result.message);
    } catch (error) { alert('System Error: ' + error.message); } finally { checkoutBtn.disabled = false; checkoutBtn.innerText = 'Checkout'; }
}

async function confirmStockTransfer(checkResult) {
    const itemsList = checkResult.items.map(item => `• ${item.product_name}: ${item.needed} units (From ${item.from_warehouse_name} to ${item.to_warehouse_name})`).join('<br>');
    const result = await Swal.fire({ title: '⚠️ Insufficient Stock', html: `<div style="text-align: left;"><p style="color: #dc3545; font-weight: bold;">Missing stock for:</p><p style="background: #f8f9fa; padding: 15px; border-radius: 5px;">${itemsList}</p><p style="margin-top: 15px;">Transfer stock and continue?</p></div>`, icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, transfer' });
    if (!result.isConfirmed) return { transferred: false, cancelled: true };
    try {
        Swal.fire({ title: 'Transferring Stock', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        const transferPromises = checkResult.items.map(async (item) => {
            if (!item.from_warehouse_id) throw new Error(`No warehouse has stock of ${item.product_name}!`);
            const response = await fetch(window.POS_CONFIG.routes.transferStock, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.POS_CONFIG.csrf }, body: JSON.stringify({ product_variant_id: item.variant_id, from_warehouse_id: item.from_warehouse_id, to_warehouse_id: item.to_warehouse_id, quantity: item.needed }) });
            if (!response.ok) throw new Error('Transfer failed');
            return await response.json();
        });
        await Promise.all(transferPromises);
        Swal.close();
        return { transferred: true };
    } catch (error) { Swal.fire({ icon: 'error', title: 'Transfer Failed', text: error.message }); return { transferred: false, error: error.message }; }
}

function selectCustomer(id, name, balance) {
    document.getElementById('customer-select').value = id;
    document.getElementById('selected-customer-name').innerText = name;
    updatePaymentOptions(id);
    closeCustomerModal();
}

function openCustomerModal() { document.getElementById('customer-modal').classList.remove('hidden'); document.getElementById('customer-modal').classList.add('flex'); setTimeout(() => document.getElementById('customer-search').focus(), 100); }
function closeCustomerModal() { document.getElementById('customer-modal').classList.add('hidden'); document.getElementById('customer-modal').classList.remove('flex'); document.getElementById('customer-search').value = ''; filterCustomers(); }
function filterCustomers() {
    const term = document.getElementById('customer-search').value.toLowerCase();
    const items = document.querySelectorAll('.customer-item');
    items.forEach(item => {
        const name = item.dataset.name || 'walk-in customer';
        item.style.display = name.includes(term) ? 'flex' : 'none';
    });
}

function updateSessionWarehouse() {
    const warehouseId = document.getElementById('session-warehouse').value;
    const warehouseName = warehouses[warehouseId] || 'Warehouse';
    // Logic to maybe refresh stock or just update label
}

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search').addEventListener('input', filterProducts);
    window.activeCategory = 'all';
});

// Exposed Globally for HTML onclicks
window.filterCategory = filterCategory;
window.openVariantPicker = openVariantPicker;
window.closeVariantPicker = closeVariantPicker;
window.setPriceMode = setPriceMode;
window.pickerChangeQty = pickerChangeQty;
window.pickerAddToCart = pickerAddToCart;
window.clearCart = clearCart;
window.updateQty = updateQty;
window.openModal = openModal;
window.closeModal = closeModal;
window.saveModal = saveModal;
window.setActiveInput = setActiveInput;
window.appendEditNumpad = appendEditNumpad;
window.popEditNumpad = popEditNumpad;
window.clearEditNumpad = clearEditNumpad;
window.addPayment = addPayment;
window.removePayment = removePayment;
window.openPaymentModal = openPaymentModal;
window.closePaymentModal = closePaymentModal;
window.appendNumpad = appendNumpad;
window.appendNumpadQuick = appendNumpadQuick;
window.setNumpadValue = setNumpadValue;
window.clearNumpad = clearNumpad;
window.popNumpad = popNumpad;
window.confirmPaymentModal = confirmPaymentModal;
window.processCheckout = processCheckout;
window.openCustomerModal = openCustomerModal;
window.closeCustomerModal = closeCustomerModal;
window.filterCustomers = filterCustomers;
window.selectCustomer = selectCustomer;
window.updateSessionWarehouse = updateSessionWarehouse;
