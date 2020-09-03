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

class NotGuestMiddleware implements MiddlewareInterface
{
    /**
     * @Inject
     *
     * @var AuthManager
     */
    protected $auth;

    protected $guards = ['token', 'admin'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->guards as $name) {
            $guard = $this->auth->guard($name);

            if ($guard && !$guard->guest()) {
                return $handler->handle($request);
            }
        }

        throw new UnauthorizedException("Without authorization from {$guard->getName()} guard");
    }
}
