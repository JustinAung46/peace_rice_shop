<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md md:max-w-3xl lg:max-w-5xl overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800" id="payment-modal-title">Payment Amount</h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="flex flex-col md:flex-row flex-1">
            <div class="w-full md:w-1/3 bg-slate-50 p-6 border-r border-slate-100 flex flex-col justify-center">
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4">Order Summary</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center text-slate-600"><span>Subtotal</span><span id="pm-subtotal" class="font-bold">0 MMK</span></div>
                    <div class="flex justify-between items-center text-slate-600"><span>Discount</span><span id="pm-discount" class="font-bold text-red-500">0 MMK</span></div>
                    <div class="pt-3 border-t border-slate-200 flex justify-between items-center"><span class="font-bold text-slate-800">Net Total</span><span id="pm-net-total" class="font-bold text-slate-800 text-base">0 MMK</span></div>
                    <div id="pm-other-payments-container" class="flex justify-between items-center text-slate-600 pt-3 border-t border-slate-200 hidden"><span>Other Payments</span><span id="pm-other-payments" class="font-bold text-emerald-600">0 MMK</span></div>
                    <div class="pt-3 border-t border-slate-200 flex justify-between items-center bg-indigo-50 p-3 rounded-lg mt-3"><span class="font-bold text-indigo-900">Remaining</span><span id="pm-remaining" class="font-bold text-indigo-700 text-lg">0 MMK</span></div>
                </div>
            </div>
            <div class="w-full md:w-2/3 p-6 flex flex-col">
                <div class="mb-4">
                    <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl text-center flex justify-between items-center">
                        <span class="text-sm font-bold text-indigo-400 uppercase tracking-wide">Paying Now</span>
                        <div class="flex items-baseline gap-2"><span id="payment-numpad-display" class="text-3xl font-black text-indigo-700">0</span><span class="text-sm font-bold text-indigo-400">MMK</span></div>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <button onclick="setNumpadValue('full_remain')" id="btn-quick-full" class="col-span-2 py-3 bg-indigo-100 text-indigo-700 rounded-lg font-bold hover:bg-indigo-200 text-xs shadow-sm">Full Amount</button>
                    <button onclick="appendNumpadQuick(1000)" class="py-3 bg-slate-100 text-slate-700 rounded-lg font-bold hover:bg-slate-200 text-xs shadow-sm">1k</button>
                    <button onclick="appendNumpadQuick(5000)" class="py-3 bg-slate-100 text-slate-700 rounded-lg font-bold hover:bg-slate-200 text-xs shadow-sm">5k</button>
                    <button onclick="appendNumpadQuick(10000)" class="py-3 bg-slate-100 text-slate-700 rounded-lg font-bold hover:bg-slate-200 text-xs shadow-sm">10k</button>
                    <button onclick="appendNumpadQuick(20000)" class="py-3 bg-slate-100 text-slate-700 rounded-lg font-bold hover:bg-slate-200 text-xs shadow-sm">20k</button>
                    <button onclick="appendNumpadQuick(50000)" class="py-3 bg-slate-100 text-slate-700 rounded-lg font-bold hover:bg-slate-200 text-xs shadow-sm">50k</button>
                    <button onclick="appendNumpadQuick(100000)" class="py-3 bg-slate-100 text-slate-700 rounded-lg font-bold hover:bg-slate-200 text-xs shadow-sm">100k</button>
                </div>
                <div class="grid grid-cols-3 gap-2 flex-1">
                    @foreach([1,2,3,4,5,6,7,8,9] as $num)
                        <button onclick="appendNumpad({{ $num }})" class="py-3 bg-white border border-slate-200 rounded-lg text-xl font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">{{ $num }}</button>
                    @endforeach
                    <button onclick="clearNumpad()" class="py-3 bg-red-50 border border-red-100 rounded-lg text-lg font-bold text-red-600 hover:bg-red-100 shadow-sm transition-colors">C</button>
                    <button onclick="appendNumpad(0)" class="py-3 bg-white border border-slate-200 rounded-lg text-xl font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">0</button>
                    <button onclick="popNumpad()" class="py-3 bg-slate-50 border border-slate-200 rounded-lg text-lg font-bold text-slate-700 hover:bg-slate-100 shadow-sm transition-colors flex justify-center items-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" /></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-3">
            <button onclick="closePaymentModal()" class="flex-1 py-4 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-all shadow-sm text-lg">Cancel</button>
            <button onclick="confirmPaymentModal()" class="flex-[2] py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all text-lg">Confirm Payment</button>
        </div>
    </div>
</div>
