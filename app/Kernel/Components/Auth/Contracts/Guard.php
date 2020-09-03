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

use App\Kernel\Components\Auth\Exception\UnauthorizedException;

interface Guard
{
    /**
     * 检查登陆需要触发错误.
     *
     * @return bool
     */
    public function check();

    /**
     * @throws UnauthorizedException
     *
     * @return Authenticatable;
     */
    public function authenticate();

    /**
     * 判断当前是否游客.
     *
     * @return bool
     */
    public function guest();

    /**
     * 返回用户.
     *
     * @return null|Authenticatable
     */
    public function user();

    /**
     * 获取当前用户的主键.
     *
     * @return null|int|string
     */
    public function id();

    /**
     * 验证当前用户凭证
     *
     * @return bool
     */
    public function validate(array $credentials = []);

    /**
     * 设置当前用户.
     */
    public function setUser(Authenticatable $user = null);
}
