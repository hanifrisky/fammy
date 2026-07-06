<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fammi API Configuration
    |--------------------------------------------------------------------------
    */

    'base_url'        => env('FAMMI_BASE_URL', 'https://a70a4199.fammi.ly/main-1.0.0'),
    'data_source_url' => env('FAMMI_DATA_SOURCE_URL', 'https://cc6bb196.fammi.ly/data-source'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Tokens
    |--------------------------------------------------------------------------
    */

    'auth_token_1'  => env('FAMMI_AUTH_TOKEN_1', '1df3746a4728276afdc24f828186f73a'),
    'auth_token_2'  => env('FAMMI_AUTH_TOKEN_2', '0d81d917ab523431ccc7173d6810fbb2'),
    'program_token' => env('FAMMI_PROGRAM_TOKEN', '1df3746a4728276afdc24f828186f73a'),
    'ref_id'        => env('FAMMI_REF_ID', 'unidentified'),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    | Jika true, data yang di-generate akan ditampilkan tanpa dikirim ke API.
    | Set FAMMI_DEBUG_MODE=false di .env untuk mode production.
    */

    'debug_mode' => env('FAMMI_DEBUG_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Data Source Slugs
    |--------------------------------------------------------------------------
    */

    'guru_data_source' => 'guru-tmlg',

    /*
    |--------------------------------------------------------------------------
    | Daftar Kelas
    |--------------------------------------------------------------------------
    | Format: 'kodeKelas' => 'Nama Kelas'
    | kodeKelas diambil dari URL https://app.fammi.ly/s/{kodeKelas}
    */

    'classes' => [
        // ── Kelas X ─────────────────────────────────
        '169b6d5451' => 'X Inter',
        '69db2d19H0' => 'X PG 1',
        'w69db2d4b1' => 'X PG 2',
        // 'w69db2d4b1' => 'X RPL 1',  // ⚠ Kode sama dengan X PG 2 — cek ulang
        '69db2d7eO0' => 'X RPL 2',
        'g69db2d981' => 'X RPL 3',
        '69d8ea3bS0' => 'X RPL 4',
        'h69d8f6251' => 'X RPL 5',
        '69db2dc200' => 'X RPL 6',
        'r69db2dd71' => 'X RPL 7',
        'X69db2de71' => 'X RPL 8',
        '69db2df6T0' => 'X TKJ 1',
        'H69db2e041' => 'X TKJ 2',
        '69db2e13e0' => 'X TKJ 3',
        'g69db2e221' => 'X TKJ 4',

        // ── Kelas XI ────────────────────────────────
        '69dc59d2i0' => 'XI PG 1',
        'f69dc5ae51' => 'XI PG 2',
        'z69d8d2af1' => 'XI RPL 1',
        '69dc5f4dr0' => 'XI RPL 2',
        '69dc5f85c0' => 'XI RPL 3',
        '969dc761c1' => 'XI RPL 4',
        'Z69dc8d8b1' => 'XI RPL 5',
        '69dc8f27b0' => 'XI RPL 6',
        '69dc8f6720' => 'XI RPL 7',
        '69dcc181h0' => 'XI RPL 8',
        'G69dcc3a51' => 'XI TKJ 1',
        'u69dcc4b21' => 'XI TKJ 2',
        'a69dcc5da1' => 'XI TKJ 3',
        '69dcc71bB0' => 'XI TKJ 4',
    ],

    /*
    |--------------------------------------------------------------------------
    | Quiz Filters
    |--------------------------------------------------------------------------
    | Kata kunci untuk memfilter quiz program yang akan diproses.
    | quiz_filters: quiz yang title-nya mengandung salah satu keyword ini akan diproses
    | quiz_exclude_filters: quiz yang title-nya mengandung keyword ini akan di-skip
    */

    'quiz_filters' => [
        'EMPATI',
        'RESILIENCE',
        'RESILIENSI',
        'INISIATIF',
        '7 KEBIASAAN',
    ],

    'quiz_exclude_filters' => [
        'Catatan Bulanan',
    ],
];
