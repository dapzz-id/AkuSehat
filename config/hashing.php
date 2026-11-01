<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver (RFC 9106 Memory-Hard Argon2id)
    |--------------------------------------------------------------------------
    |
    | Menggunakan Argon2id secara default (dengan fallback otomatis ke bcrypt
    | apabila ekstensi Argon2id tidak tersedia pada binary PHP tertentu).
    |
    */

    'driver' => env('HASH_DRIVER', defined('PASSWORD_ARGON2ID') ? 'argon2id' : 'bcrypt'),

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', false),
        'limit' => env('BCRYPT_LIMIT', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon2id Options (RFC 9106 / OWASP GPU-Cracking Resistant Parameters)
    |--------------------------------------------------------------------------
    |
    | - memory: 65536 KB (64 MiB) per hash untuk menghentikan paralelisme GPU/ASIC
    | - time: 4 iterasi
    | - threads: 1 lane
    | - verify: false agar akun lama berformat Bcrypt ($2y$) tetap dapat login
    |           dan otomatis di-upgrade (rehash) ke Argon2id ($argon2id$).
    |
    */

    'argon' => [
        'memory' => (int) env('ARGON_MEMORY', 65536),
        'threads' => (int) env('ARGON_THREADS', 1),
        'time' => (int) env('ARGON_TIME', 4),
        'verify' => (bool) env('HASH_VERIFY', false),
    ],

    'rehash_on_login' => true,

];

