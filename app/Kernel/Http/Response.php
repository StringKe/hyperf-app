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
namespace App\Kernel\Http;

use App\Constants\AppCode;
use App\Kernel\Components\Resource\Json\JsonResource;
use Carbon\Carbon;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class Response
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(ResponseInterface::class);
    }

    public function fail($code, $data = [], $message = '')
    {
        $message = mb_strlen($message) > 0 ? $message : AppCode::getMessage($code);

        return $this->success($data, $message, $code);
    }

    public function success($data = [], $message = null, $code = 1000)
    {
        if ($data instanceof JsonResource) {
            $data->with = array_merge($this->apiData($code, $message), $data->with);

            return $data;
        }

        return $this->response->json(array_merge($this->apiData($code, $message), ['data' => $data]));
    }

    public function redirect($url, $status = 302)
    {
        return $this->response()
            ->withAddedHeader('Location', (string) $url)
            ->withStatus($status)
        ;
    }

    /**
     * @return \Hyperf\HttpMessage\Server\Response
     */
    public function response()
    {
        return Context::get(PsrResponseInterface::class);
    }

    public function cookie(Cookie $cookie)
    {
        $response = $this->response()->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);

        return $this;
    }

    protected function apiData($code, $message)
    {
        return [
            'code' => $code,
            'message' => $message ?: AppCode::getMessage($code),
            '__' => [
                'time' => Carbon::now()->timestamp,
                'request' => Context::get('request_id'),
            ],
        ];
    }
}
