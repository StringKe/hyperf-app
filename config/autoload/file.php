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
use App\Kernel\Components\AliyunOSS\OssFactory;
use Hyperf\Filesystem\Adapter\LocalAdapterFactory;

return [
    'default' => 'local',
    'storage' => [
        'local' => [
            'driver' => LocalAdapterFactory::class,
            'root' => __DIR__.'/../../fakeOSS/assets',
        ],
        'spider' => [
            'driver' => LocalAdapterFactory::class,
            'root' => __DIR__.'/../../spider',
        ],
        'template' => [
            'driver' => OssFactory::class,
            'accessId' => ' ',
            'accessSecret' => ' ',
            'bucket' => ' ',
            'endpoint' => ' ',
            'timeout' => 3600,
            'connectTimeout' => 10,
            'isCName' => false,
        ],
    ],
];
