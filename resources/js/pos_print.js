/**
 * POS Print Bridge
 *
 * This file handles the communication between the POS server and the Android Container App.
 * The receipt design is now generated in the backend (ReceiptFormatter.php).
 */

/**
 * Constructs the receipt JSON object to be sent to the Android bridge.
 * 
 * @param {object} saleResult - The full JSON response from the server.
 */
function buildReceiptData(saleResult) {
    const serverReceipt = saleResult.receipt || {};
    
    return {
        // Core sale data (for logging/fallback in app)
        invoiceNumber: serverReceipt.invoiceNumber || saleResult.invoice || '',
        dateTime:      serverReceipt.dateTime      || new Date().toLocaleString(),
        customerName:  serverReceipt.customerName  || 'Walk-in Customer',
        items:         serverReceipt.items         || [],
        subtotal:      serverReceipt.subtotal      || 0,
        discount:      serverReceipt.discount      || 0,
        total:         serverReceipt.total         || 0,
        payments:      serverReceipt.payments      || [],
        
        // The formatted layout from backend (Primary source for printing)
        formatted_receipt: serverReceipt.formatted_receipt || saleResult.formatted_receipt || null
    };
}

/**
 * Handles the print dialog and sends data to the Android App.
 */
function handlePrint(result, onDone) {
    // Show print dialog if the Android bridge exists at all.
    // isPrinterConfigured() may return false even when a printer is paired,
    // so we only require that window.Android is present.
    const hasAndroidBridge = typeof window.Android !== 'undefined' && window.Android !== null;
    const canPrint = hasAndroidBridge && typeof window.Android.printReceipt === 'function';

    if (hasAndroidBridge) {
        Swal.fire({
            icon:              'success',
            title:             'Sale Successful!',
            html:              '<p style="margin:0 0 6px">Invoice: <strong>' + result.invoice + '</strong></p>'
                             + '<p style="margin:0;color:#6366f1;font-size:0.9em">Print receipt?</p>',
            showConfirmButton: true,
            confirmButtonText: '🖨️ Print Receipt',
            confirmButtonColor:'#4f46e5',
            showDenyButton:    true,
            denyButtonText:    'Skip',
            denyButtonColor:   '#94a3b8',
            allowOutsideClick: false,
            timer:             15000,
            timerProgressBar:  true,
        }).then((swalResult) => {
            if (swalResult.isConfirmed) {
                try {
                    if (!canPrint) {
                        console.warn('Android.printReceipt() is not available.');
                        Swal.fire({ icon: 'warning', title: 'Printer Not Ready', text: 'Please configure a Bluetooth printer in the app settings.', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                    } else {
                        const receiptData = buildReceiptData(result);
                        window.Android.printReceipt(JSON.stringify(receiptData));
                    }
                } catch (e) {
                    console.error('Print error:', e);
                    Swal.fire({ icon: 'error', title: 'Print Failed', text: e.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                }
            }
            if (typeof onDone === 'function') onDone();
        });
    } else {
        Swal.fire({
            icon:             'success',
            title:            'Sale Successful!',
            text:             'Invoice: ' + result.invoice,
            timer:            3000,
            timerProgressBar: true,
            showConfirmButton: true,
            confirmButtonText: 'OK',
        }).then(() => {
            if (typeof onDone === 'function') onDone();
        });
    }
}

// Expose globally
window.handlePrint = handlePrint;
