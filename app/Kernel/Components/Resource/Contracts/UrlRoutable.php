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
namespace App\Kernel\Components\Resource\Contracts;

interface UrlRoutable
{
    public function getRouteKey();

    public function getRouteKeyName();

    public function resolveRouteBinding($value, $field = null);

    public function resolveChildRouteBinding($childType, $value, $field);
}
