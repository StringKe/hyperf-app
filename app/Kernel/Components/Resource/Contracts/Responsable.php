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

use Hyperf\HttpServer\Contract\RequestInterface;

interface Responsable
{
    /**
     * @return mixed
     */
    public function toResponse(RequestInterface $request);
}
