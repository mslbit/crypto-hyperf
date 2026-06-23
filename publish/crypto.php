<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    /*
    |--------------------------------------------------------------------------
    | 默认驱动
    |--------------------------------------------------------------------------
    */
    'default' => [
        'hasher' => 'bcrypt',
        'encrypter' => 'openssl',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hasher 配置（单向哈希）
    |--------------------------------------------------------------------------
    |
    | driver: bcrypt / argon2 / sodium 或实现 HasherFactoryInterface 的类名
    |
    */
    'hashers' => [
        'bcrypt' => [
            'driver' => 'bcrypt',
            'cost' => 12,
        ],
        'argon2' => [
            'driver' => 'argon2',
        ],
        'sodium' => [
            'driver' => 'sodium',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Encrypter 配置（双向加解密）
    |--------------------------------------------------------------------------
    |
    | driver:
    |   openssl   - AES-256-GCM（认证加密，推荐内部使用）
    |   aes-cbc   - AES-CBC-PKCS7（Java/企业对接标准）
    |   sodium    - XChaCha20-Poly1305（libsodium 认证加密）
    |   rsa       - RSA 非对称加密（密钥交换/签名验签）
    |   或实现 EncrypterFactoryInterface 的类名
    |
    | 生成密钥：php bin/hyperf.php crypto:generate-key
    |
    */
    'encrypters' => [
        'openssl' => [
            'driver' => 'openssl',
            'key' => env('CRYPTO_OPENSSL_KEY', 'base64:Y2hhbmdlLXRoaXMtdG8tYS1yZWFsLWtleQ=='),
            'cipher' => 'aes-256-gcm',
        ],
        'aes-cbc' => [
            'driver' => 'aes-cbc',
            'key' => env('CRYPTO_AES_CBC_KEY', 'base64:Y2hhbmdlLXRoaXMtdG8tYS1yZWFsLWtleQ=='),
            'cipher' => 'aes-256-cbc',
        ],
        'sodium' => [
            'driver' => 'sodium',
            'key' => env('CRYPTO_SODIUM_KEY', 'base64:Y2hhbmdlLXRoaXMtdG8tYS1yZWFsLWtleQ=='),
        ],
        'rsa' => [
            'driver' => 'rsa',
            'private_key' => env('CRYPTO_RSA_PRIVATE_KEY', ''),
            'public_key' => env('CRYPTO_RSA_PUBLIC_KEY', ''),
            'padding' => 'oaep',
            'sign_algo' => 'sha256',
        ],
    ],
];
