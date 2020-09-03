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
namespace App\Controller;

use App\Kernel\Components\Auth\Contracts\Guard;
use App\Kernel\Http\Response;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;

abstract class Controller
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(Response::class);
        $this->request = $container->get(RequestInterface::class);
    }

    /**
     * @return null|int|string
     */
    protected function userId()
    {
        if ($this->auth() && $this->auth()->user()) {
            return $this->auth()->user()->getAuthIdentifier();
        }

        return null;
    }

    /**
     * @return Guard
     */
    protected function auth()
    {
        return auth()->guard('token');
    }
}
