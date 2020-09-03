<?php

declare(strict_types=1);
/**
 * 文件通用头部提示
 * 注意：请保持规范，文件注释
 *
 * 文件说明
 *
 *
 * 文件记录
 *
 *
 */
use App\Kernel\Components\Auth\Guard\ApiTokenGuard;
use App\Kernel\Components\Auth\Guard\TokenGuard;
use App\Kernel\Components\Auth\Provider\DatabaseProvider;

return [
    'defaults' => [
        'guard' => 'token',
        'provider' => 'users',
    ],
    'guards' => [
        'token' => [
            'driver' => TokenGuard::class,
            'provider' => 'users',
        ],
        'admin' => [
            'driver' => ApiTokenGuard::class,
            'provider' => 'admin',
            'input_key' => 'admin_token',
            'storage_key' => 'api_token',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => DatabaseProvider::class,
            'table' => 'users',
        ],
        'admin' => [
            'driver' => DatabaseProvider::class,
            'table' => 'users',
        ],
    ],
];
