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

use Hyperf\Paginator\AbstractPaginator;
use Hyperf\Utils\Collection;
use Hyperf\Utils\Str;

trait CollectsResources
{
    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    protected function collectResource($resource)
    {
        if ($resource instanceof MissingValue) {
            return $resource;
        }

        if (is_array($resource)) {
            $resource = new Collection($resource);
        }

        $collects = $this->collects();

        $this->collection = $collects && !$resource->first() instanceof $collects
            ? $resource->mapInto($collects)
            : $resource->toBase();

        if ($resource instanceof AbstractPaginator) {
            return $resource->setCollection($this->collection);
        }

        return $this->collection;
    }

    protected function collects()
    {
        if ($this->collects) {
            return $this->collects;
        }

        if (Str::endsWith(class_basename($this), 'Collection') &&
            class_exists($class = Str::replaceLast('Collection', '', get_class($this)))) {
            return $class;
        }
    }
}
