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

use Exception;
use Hyperf\Utils\Traits\ForwardsCalls;

trait DelegatesToResource
{
    use ForwardsCalls;

    public function __isset($key)
    {
        return isset($this->resource->{$key});
    }

    public function __unset($key)
    {
        unset($this->resource->{$key});
    }

    public function __get($key)
    {
        return $this->resource->{$key};
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->resource, $method, $parameters);
    }

    public function getRouteKey()
    {
        return $this->resource->getRouteKey();
    }

    public function getRouteKeyName()
    {
        return $this->resource->getRouteKeyName();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        throw new Exception('Resources may not be implicitly resolved from route bindings.');
    }

    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        throw new Exception('Resources may not be implicitly resolved from route bindings.');
    }

    public function offsetExists($offset)
    {
        return isset($this->resource[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->resource[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->resource[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->resource[$offset]);
    }
}
