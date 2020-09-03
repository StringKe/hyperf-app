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
use App\Kernel\Http\WorkerStartListener;
use App\Kernel\Middleware\CoreMiddleware;

return [
    Hyperf\Server\Listener\AfterWorkerStartListener::class => WorkerStartListener::class,
    Hyperf\HttpServer\CoreMiddleware::class => CoreMiddleware::class,
];
