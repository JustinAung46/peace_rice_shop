<!-- Edit Item Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh] overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800" id="modal-item-name">Edit Item</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <input type="hidden" id="modal-item-id">
        <div class="flex-1 flex flex-col md:flex-row bg-slate-50 relative">
            <div class="p-5 space-y-4 flex-1 border-b md:border-b-0 md:border-r border-slate-200 bg-white">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Unit Price (MMK)</label>
                    <div id="input-btn-price" onclick="setActiveInput('price')" class="w-full px-4 py-4 border-2 border-indigo-500 bg-indigo-50 rounded-xl text-right font-black text-2xl text-indigo-900 cursor-pointer transition-colors shadow-inner">0</div>
                    <input type="hidden" id="modal-price">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Quantity</label>
                        <div id="input-btn-qty" onclick="setActiveInput('qty')" class="w-full px-4 py-4 border-2 border-slate-200 bg-white rounded-xl text-center font-bold text-xl text-slate-800 cursor-pointer transition-colors hover:border-indigo-300">0</div>
                        <input type="hidden" id="modal-qty">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Discount</label>
                        <div id="input-btn-discount" onclick="setActiveInput('discount')" class="w-full px-4 py-4 border-2 border-slate-200 bg-white rounded-xl text-right font-bold text-xl text-slate-800 cursor-pointer transition-colors hover:border-indigo-300">0</div>
                        <input type="hidden" id="modal-discount">
                    </div>
                </div>
                <div class="pt-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Warehouse</label>
                    <select id="modal-warehouse" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-bold text-slate-700 shadow-sm cursor-pointer border-r-8 border-transparent">
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="p-5 flex-[0.8] flex flex-col justify-center">
                <div class="grid grid-cols-3 gap-3">
                    @foreach([1,2,3,4,5,6,7,8,9] as $num)
                        <button onclick="appendEditNumpad('{{ $num }}')" class="py-5 bg-white border border-slate-200 rounded-xl text-2xl font-black text-slate-700 hover:bg-indigo-50 active:bg-indigo-100 shadow-sm transition-all">{{ $num }}</button>
                    @endforeach
                    <button onclick="appendEditNumpad('00')" class="py-5 bg-white border border-slate-200 rounded-xl text-2xl font-black text-slate-700 hover:bg-slate-100 active:bg-slate-200 shadow-sm transition-all leading-none pt-2">00</button>
                    <button onclick="appendEditNumpad('0')" class="py-5 bg-white border border-slate-200 rounded-xl text-2xl font-black text-slate-700 hover:bg-indigo-50 active:bg-indigo-100 shadow-sm transition-all">0</button>
                    <button onclick="popEditNumpad()" class="py-5 bg-red-50 border border-red-100 rounded-xl text-lg font-bold text-red-600 hover:bg-red-100 active:bg-red-200 shadow-sm transition-all flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" /></svg>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <button onclick="clearEditNumpad()" class="py-4 bg-slate-200 border border-slate-300 rounded-xl text-base font-bold text-slate-700 hover:bg-slate-300 active:bg-slate-400 transition-all shadow-sm">Clear</button>
                    <button onclick="appendEditNumpad('000')" class="py-4 bg-white border border-slate-200 rounded-xl text-lg font-black text-slate-700 hover:bg-indigo-50 active:bg-indigo-100 shadow-sm transition-all">000</button>
                </div>
            </div>
        </div>
        <div class="p-4 bg-white border-t border-slate-100 flex gap-3">
            <button onclick="closeModal()" class="flex-1 py-4 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-all shadow-sm text-lg">Cancel</button>
            <button onclick="saveModal()" class="flex-[2] py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all text-lg">Save Changes</button>
        </div>
    </div>
</div>
