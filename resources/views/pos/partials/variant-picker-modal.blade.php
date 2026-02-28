<!-- Variant Picker Modal -->
<div id="variant-picker-modal" class="fixed inset-0 bg-black/40 z-[80] hidden items-center justify-center p-4 md:p-6 transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl lg:max-w-4xl flex flex-col overflow-hidden animate-in fade-in zoom-in duration-300 max-h-[90vh] md:max-h-[85vh]">
        <div class="flex items-center justify-between p-5 md:p-7 border-b border-slate-100 flex-shrink-0">
            <h3 id="variant-picker-title" class="text-lg md:text-xl lg:text-2xl font-bold text-slate-900 truncate pr-4"></h3>
            <button onclick="closeVariantPicker()" class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-all flex-shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 md:p-7">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
                <div class="lg:w-1/3 flex justify-center">
                    <div class="w-48 h-48 md:w-56 md:h-56 lg:w-full lg:h-48 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 shadow-md flex-shrink-0">
                        <img id="picker-img" src="" alt="" class="w-full h-full object-cover hidden">
                        <div id="picker-img-placeholder" class="w-full h-full flex items-center justify-center bg-slate-50">
                            <svg class="w-16 h-16 md:w-20 md:h-20 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex-1 space-y-6">
                    <section>
                        <h4 class="text-xs md:text-sm font-medium text-slate-500 uppercase tracking-wider mb-3">Price Type</h4>
                        <div class="flex gap-2 p-1 bg-slate-100 rounded-xl">
                            <button id="price-mode-wholesale" onclick="setPriceMode('wholesale')"
                                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all bg-white text-indigo-700 shadow-sm border border-indigo-100">Wholesale</button>
                            <button id="price-mode-retail" onclick="setPriceMode('retail')"
                                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all text-slate-600 hover:bg-slate-50">Retail</button>
                        </div>
                    </section>
                    <section>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs md:text-sm font-medium text-slate-500 uppercase tracking-wider">Select Variant</h4>
                            <div class="flex flex-col items-end gap-1">
                                <span id="picker-stock-label" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full hidden transition-all"></span>
                                <div id="picker-warehouse-breakdown" class="text-[10px] font-medium text-slate-400 text-right hidden"></div>
                            </div>
                        </div>
                        <div id="variant-picker-pills" class="grid grid-cols-2 sm:grid-cols-3 gap-3 pr-2"></div>
                    </section>

                </div>
            </div>
        </div>
        <div class="p-5 md:p-6 lg:p-7 border-t border-slate-100 bg-slate-50/50 flex-shrink-0">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Left: Qty and Total -->
                <div class="flex flex-wrap items-center justify-between md:justify-start gap-6 w-full md:w-auto">
                    <div class="flex items-center bg-white rounded-2xl border border-slate-200 shadow-sm p-1">
                        <button onclick="pickerChangeQty(-1)" class="w-10 h-10 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M20 12H4"/></svg>
                        </button>
                        <span id="picker-qty-display" class="w-12 text-center text-xl font-black text-slate-800">1</span>
                        <button onclick="pickerChangeQty(1)" class="w-10 h-10 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all flex items-center justify-center shadow-md shadow-indigo-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    <div class="text-right md:text-left">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1.5">Total Amount</p>
                        <p id="picker-total-display" class="text-2xl md:text-3xl font-black text-slate-900 leading-none">0 <span class="text-sm font-bold text-slate-400">MMK</span></p>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button onclick="closeVariantPicker()" class="hidden md:block px-6 py-4 bg-white text-slate-500 rounded-2xl font-bold text-base border border-slate-200 hover:bg-slate-50 transition-all">Cancel</button>
                    <button id="picker-add-btn" onclick="pickerAddToCart()" disabled class="flex-1 md:flex-none px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg flex items-center justify-center gap-3 hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-30 shadow-xl shadow-indigo-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                        Add to Cart
                    </button>
                </div>
            </div>
            <p id="picker-validation-msg" class="text-center text-rose-500 text-[10px] font-bold mt-4 opacity-0 transition-opacity uppercase tracking-widest hidden">Please select a variant</p>
        </div>
    </div>
</div>
