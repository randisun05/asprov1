<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Biaya keanggotaan berbeda per jabatan - isi nilai asli (Rupiah) di .env
    // sebelum fitur ini dipakai. 'lainnya' dipakai untuk posisi di luar dua
    // jabatan resmi (anggota luar biasa), sesuai logika suffix '/LB/ASPROSDMA'
    // di RegistrationController::approve().
    'registration_fees' => [
        'Analis SDM Aparatur' => env('MIDTRANS_FEE_ANALIS', 0),
        'Pranata SDM Aparatur' => env('MIDTRANS_FEE_PRANATA', 0),
        'lainnya' => env('MIDTRANS_FEE_LAINNYA', 0),
    ],
];
