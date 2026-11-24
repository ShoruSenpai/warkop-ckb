<?php

return [
    'default_password' => env('SEED_DEFAULT_PASSWORD'),
    'opening_purchase_status' => env('SEED_OPENING_PURCHASE_STATUS', 'received'),

    'accounts' => [
        'developer_owner' => env('SEED_PASSWORD_DEVELOPER_OWNER'),
        'owner' => env('SEED_PASSWORD_OWNER'),
        'admin' => env('SEED_PASSWORD_ADMIN'),
        'barista' => env('SEED_PASSWORD_BARISTA'),
        'cashier_main' => env('SEED_PASSWORD_CASHIER_MAIN'),
        'cashier_cece' => env('SEED_PASSWORD_CASHIER_CECE'),
    ],
];
