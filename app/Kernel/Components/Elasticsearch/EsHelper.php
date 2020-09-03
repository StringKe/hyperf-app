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
namespace App\Kernel\Components\Elasticsearch;

use Hyperf\Paginator\Paginator;

class EsHelper
{
    public static function pageInfo()
    {
        $page = Paginator::resolveCurrentPage();
        $pageSize = Paginator::resolveCurrentPageSize();

        return [
            'from' => ($page - 1) * $pageSize,
            'size' => $pageSize,
            'track_total_hits' => true,
        ];
    }
}
