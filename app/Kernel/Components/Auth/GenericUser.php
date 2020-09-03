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
namespace App\Kernel\Components\Auth;

use App\Kernel\Components\Auth\Contracts\Authenticatable;

class GenericUser implements Authenticatable
{
    protected $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function __get($key)
    {
        return $this->attributes[$key];
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    public function getAuthIdentifier()
    {
        return (string) $this->attributes[$this->getAuthIdentifierName()];
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthToken()
    {
        return (string) $this->attributes['password'];
    }
}
