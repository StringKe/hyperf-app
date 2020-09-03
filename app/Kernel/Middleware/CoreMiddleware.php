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
namespace App\Kernel\Middleware;

use App\Constants\AppCode;
use App\Kernel\Components\Resource\Json\JsonResource;
use App\Kernel\Http\Response;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Contracts\Jsonable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    /**
     * @var Response
     * @Inject
     */
    protected $response;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Context::getOrSet('request_id', md5(uniqid()));

        return parent::process($request, $handler);
    }

    protected function handleFound(Dispatched $dispatched, ServerRequestInterface $request)
    {
        return parent::handleFound($dispatched, $request);
    }

    protected function handleNotFound(ServerRequestInterface $request)
    {
        return $this->response->fail(AppCode::ROUTE_ERROR, [], AppCode::getMessage(AppCode::ROUTE_ERROR));
    }

    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request)
    {
        return $this->response->fail(AppCode::ROUTE_HANDEL_ERROR, [], AppCode::getMessage(AppCode::ROUTE_HANDEL_ERROR));
    }

    protected function transferToResponse($response, ServerRequestInterface $request): ResponseInterface
    {
        if (is_string($response)) {
            return $this->response()->withAddedHeader('content-type', 'text/plain')->withBody(new SwooleStream($response));
        }

        if (is_array($response) || $response instanceof Arrayable) {
            if ($response instanceof Arrayable) {
                $response = $response->toArray();
            }

            return $this->response()
                ->withAddedHeader('content-type', 'application/json')
                ->withBody(new SwooleStream(json_encode($response, JSON_UNESCAPED_UNICODE)))
            ;
        }

        if ($response instanceof JsonResource) {
            return $response->response($request);
        }

        if ($response instanceof Jsonable) {
            return $this->response()
                ->withAddedHeader('content-type', 'application/json')
                ->withBody(new SwooleStream((string) $response))
            ;
        }

        return $this->response()->withAddedHeader('content-type', 'text/plain')->withBody(new SwooleStream((string) $response));
    }
}
