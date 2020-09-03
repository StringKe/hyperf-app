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
namespace App\Kernel\Components\Resource\Json;

class AnonymousResourceCollection extends ResourceCollection
{
    /**
     * @var string
     */
    public $collects;

    /**
     * AnonymousResourceCollection constructor.
     *
     * @param mixed  $resource
     * @param string $collects
     */
    public function __construct($resource, $collects)
    {
        $this->collects = $collects;

        parent::__construct($resource);
    }
}
