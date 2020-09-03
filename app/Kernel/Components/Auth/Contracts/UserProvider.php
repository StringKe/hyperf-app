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

interface UserProvider
{
    /**
     * 通过主键检索用户.
     *
     * @param mixed $identifier
     *
     * @return null|Authenticatable
     */
    public function retrieveById($identifier);

    /**
     * 根据凭证返回用户.
     *
     * @return null|Authenticatable
     */
    public function retrieveByCredentials(array $credentials);

    /**
     * 根据凭证验证用户.
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials);
}
