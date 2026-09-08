<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tax Invoice — Company (Seller) Details
    |--------------------------------------------------------------------------
    |
    | Values printed in the seller/company section of the Tax Invoice.
    | Override via .env (COMPANY_NAME / COMPANY_GSTIN / COMPANY_ADDRESS) and
    | re-run "php artisan config:clear" (or config:cache); no invoice code
    | change is required.
    |
    | Company phone and email on the invoice come from the database
    | (tbl_contact_info) via the ContactInfo model.
    |
    */

    'company' => [
        'name' => env('COMPANY_NAME', 'STUDYNEST EDUCATIONAL'),
        'gstin' => env('COMPANY_GSTIN', '23DCUPS1846F1Z8'),
        'address' => env('COMPANY_ADDRESS', 'F.No-206, Block No. B, Sagar Golden Palms,
Katara Hills Barai Road, Near St. Francis Co-Ed School,
Bhopal, Madhya Pradesh – 462043'),
    ],

];