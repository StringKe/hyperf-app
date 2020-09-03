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
use App\Exception\Handler\BusinessExceptionHandler;
use App\Exception\Handler\ValidationExceptionHandler;
use App\Kernel\Components\Auth\Exception\AuthExceptionHandler;

return [
    'handler' => [
        'http' => [
            ValidationExceptionHandler::class,
            AuthExceptionHandler::class,
            BusinessExceptionHandler::class,
        ],
    ],
];
