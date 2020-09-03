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

use App\Kernel\Components\Resource\ConditionallyLoadsAttributes;
use App\Kernel\Components\Resource\Contracts\Responsable;
use App\Kernel\Components\Resource\Contracts\UrlRoutable;
use App\Kernel\Components\Resource\DelegatesToResource;
use ArrayAccess;
use Hyperf\Database\Model\JsonEncodingException;
use Hyperf\Database\Model\Model;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Paginator\LengthAwarePaginator;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Collection;
use Hyperf\Utils\Context;
use Hyperf\Utils\Contracts\Arrayable;
use JsonSerializable;

class JsonResource implements ArrayAccess, JsonSerializable, Responsable, UrlRoutable
{
    use ConditionallyLoadsAttributes;
    use DelegatesToResource;

    /**
     * @var string
     */
    public static $wrap = 'data';
    /**
     * @var mixed
     */
    public $resource;
    /**
     * @var array
     */
    public $with = [];
    /**
     * @var array
     */
    public $additional = [];

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public static function make(...$parameters)
    {
        return new self(...$parameters);
    }

    /**
     * @param Collection|LengthAwarePaginator|Model $resource
     *
     * @return AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = true === (new self([]))->preserveKeys;
            }
        });
    }

    public static function withoutWrapping()
    {
        static::$wrap = null;
    }

    public static function wrap($value)
    {
        static::$wrap = $value;
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize()
    {
        $container = ApplicationContext::getContainer();
        $request = $container->get(RequestInterface::class);

        return $this->resolve($request);
    }

    public function resolve($request)
    {
        $data = $this->toArray();

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        } elseif ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        return $this->filter((array) $data);
    }

    public function toArray(): array
    {
        if (is_null($this->resource)) {
            return [];
        }

        if (is_array($this->resource)) {
            return $this->resource;
        }

        return $this->resource->toArray();
    }

    /**
     * @param RequestInterface $request
     *
     * @return array
     */
    public function with($request)
    {
        return $this->with;
    }

    public function additional(array $data)
    {
        $this->additional = $data;

        return $this;
    }

    public function withResponse($request, $response)
    {
    }

    /**
     * @param null $request
     *
     * @return mixed|ResponseInterface
     */
    public function response($request = null)
    {
        return $this->toResponse(
            $request ?: Context::get(RequestInterface::class)
        );
    }

    /**
     * @param RequestInterface $request
     *
     * @return mixed|ResponseInterface
     */
    public function toResponse($request)
    {
        return (new ResourceResponse($this))->toResponse($request);
    }
}
