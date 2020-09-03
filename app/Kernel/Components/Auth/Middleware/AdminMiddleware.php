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
namespace App\Kernel\Components\Auth\Middleware;

use App\Kernel\Components\Auth\AuthManager;
use App\Kernel\Components\Auth\Exception\UnauthorizedException;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * @Inject
     *
     * @var AuthManager
     */
    protected $auth;

    protected $guards = ['admin'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $guard = $this->auth->guard('admin');

        if ($guard && $guard->guest()) {
            throw new UnauthorizedException("Without authorization from {$guard->getName()} guard");
        }

        return $handler->handle($request);
    }
}
