<?php

return [
    'admin_email' => env('SHOP_ADMIN_EMAIL'),
    'admin_password' => env('SHOP_ADMIN_PASSWORD'),
    'shipping_fee' => (int) env('SHOP_SHIPPING_FEE', 12000),
    'whatsapp_number' => env('SHOP_WHATSAPP_NUMBER') ?: '6281222720099',
    'contact_phone' => '081222720099',
];
