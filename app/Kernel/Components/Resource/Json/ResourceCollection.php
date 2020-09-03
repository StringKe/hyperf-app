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

use App\Kernel\Components\Resource\CollectsResources;
use Countable;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Paginator\AbstractPaginator;
use Hyperf\Utils\Collection;
use Hyperf\Utils\HigherOrderTapProxy;
use IteratorAggregate;

class ResourceCollection extends JsonResource implements Countable, IteratorAggregate
{
    use CollectsResources;

    /**
     * @var string
     */
    public $collects;

    /**
     * @var Collection
     */
    public $collection;

    /**
     * @var bool
     */
    protected $preserveAllQueryParameters = false;

    /**
     * @var array
     */
    protected $queryParameters;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);
    }

    public function preserveQuery()
    {
        $this->preserveAllQueryParameters = true;

        return $this;
    }

    public function withQuery(array $query)
    {
        $this->preserveAllQueryParameters = false;

        $this->queryParameters = $query;

        return $this;
    }

    public function count()
    {
        return $this->collection->count();
    }

    public function toArray(): array
    {
        return $this->collection->map->toArray()->all();
    }

    /**
     * @param RequestInterface $request
     *
     * @return null|HigherOrderTapProxy|mixed
     */
    public function toResponse($request)
    {
        if ($this->resource instanceof AbstractPaginator) {
            return $this->preparePaginatedResponse($request);
        }

        return parent::toResponse($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return null|HigherOrderTapProxy|mixed
     */
    protected function preparePaginatedResponse($request)
    {
        if ($this->preserveAllQueryParameters) {
            $this->resource->appends($request->query());
        } elseif (!is_null($this->queryParameters)) {
            $this->resource->appends($this->queryParameters);
        }

        return (new PaginatedResourceResponse($this))->toResponse($request);
    }
}
