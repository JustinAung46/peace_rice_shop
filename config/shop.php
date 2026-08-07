<?php

/*
|--------------------------------------------------------------------------
| Shop Configuration
|--------------------------------------------------------------------------
|
| These values are used by ReceiptFormatter to build the receipt header
| and footer. They are read from the `settings` table at runtime so the
| cashier can change them from the admin panel without any code deployment.
|
| IMPORTANT: Do NOT call Setting::get() directly here — config files are
| loaded early in the boot cycle before Eloquent is available. Instead,
| the values are resolved lazily inside ReceiptFormatter via Setting::get().
| This file only defines the hardcoded defaults.
|
*/

return [

    'name'     => 'ငြိမ်းချမ်း',
    'subtitle' => 'ဆန်ရောင်းဝယ်ရေး',
    'address'  => 'ရပ်ကွက်(၉) ဘူတာလမ်း လားရှိုးမြို့',
    'phone'    => '09-788024237, 09-5370682',
    'footer1'  => 'Thank You, Please Come Again',
    'footer2'  => 'Powered by Peace POS',

];
