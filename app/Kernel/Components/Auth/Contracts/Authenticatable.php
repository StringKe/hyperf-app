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
namespace App\Kernel\Components\Auth\Contracts;

interface Authenticatable
{
    /**
     * 获取主键名字.
     *
     * @return string
     */
    public function getAuthIdentifierName();

    /**
     * 获取唯一用户.
     *
     * @return mixed
     */
    public function getAuthIdentifier();

    /**
     * 获取用户 token/密码
     *
     * @return string
     */
    public function getAuthToken();
}
