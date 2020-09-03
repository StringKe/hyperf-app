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
use Hyperf\Paginator\AbstractPaginator;
use Hyperf\Paginator\LengthAwarePaginator;
use Hyperf\Paginator\Listener\PageResolverListener;
use Hyperf\Paginator\Paginator;
use Hyperf\Paginator\UrlWindow;

return [
    'scan' => [
        'paths' => [
            BASE_PATH.'/app',
        ],
        'ignore_annotations' => [
            'mixin',
        ],
        'class_map' => [
            PageResolverListener::class => BASE_PATH.'/app/Kernel/Components/Paginator/PageResolverListener.php',
            AbstractPaginator::class => BASE_PATH.'/app/Kernel/Components/Paginator/AbstractPaginator.php',
            LengthAwarePaginator::class => BASE_PATH.'/app/Kernel/Components/Paginator/LengthAwarePaginator.php',
            UrlWindow::class => BASE_PATH.'/app/Kernel/Components/Paginator/UrlWindow.php',
            Paginator::class => BASE_PATH.'/app/Kernel/Components/Paginator/Paginator.php',
        ],
    ],
];
