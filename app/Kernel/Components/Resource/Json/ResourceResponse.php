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

use App\Kernel\Components\Resource\Contracts\Responsable;
use App\Model\Contracts\ModelNoSoftDeletes;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Collection;

class ResourceResponse implements Responsable
{
    /**
     * @var JsonResource
     */
    public $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function toResponse($request)
    {
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);

        $response = $response->json($this->wrapData($request));

        $response = $response->withStatus($this->calculateStatus());

        return tap($response, function ($response) use ($request) {
            $response->original = $this->resource->resource;

            $this->resource->withResponse($request, $response);
        });
    }

    public function wrapData($request)
    {
        return $this->wrap(
            $this->resource->resolve($request),
            $this->resource->with($request),
            $this->resource->additional
        );
    }

    protected function wrap($data, $with = [], $additional = [])
    {
        if ($data instanceof Collection) {
            $data = $data->all();
        }

        if ($this->haveDefaultWrapperAndDataIsUnwrapped($data)) {
            $data = [$this->wrapper() => $data];
        } elseif ($this->haveAdditionalInformationAndDataIsUnwrapped($data, $with, $additional)) {
            $data = [($this->wrapper() ?? 'data') => $data];
        }

        return array_merge_recursive($data, $with, $additional);
    }

    protected function haveDefaultWrapperAndDataIsUnwrapped($data)
    {
        return $this->wrapper() && !array_key_exists($this->wrapper(), $data);
    }

    protected function wrapper()
    {
        return get_class($this->resource)::$wrap;
    }

    protected function haveAdditionalInformationAndDataIsUnwrapped($data, $with, $additional)
    {
        return (!empty($with) || !empty($additional)) &&
            (!$this->wrapper() ||
                !array_key_exists($this->wrapper(), $data));
    }

    protected function calculateStatus()
    {
        return $this->resource->resource instanceof ModelNoSoftDeletes &&
        $this->resource->resource->wasRecentlyCreated ? 201 : 200;
    }
}
