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
namespace App\Kernel\Components\Resource;

use App\Kernel\Components\Resource\Contracts\PotentiallyMissing;

class MissingValue implements PotentiallyMissing
{
    public function isMissing()
    {
        return true;
    }
}
