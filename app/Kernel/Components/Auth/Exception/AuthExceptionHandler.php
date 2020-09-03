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
namespace App\Kernel\Components\Auth\Exception;

use App\Constants\AppCode;
use App\Kernel\Http\Response;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AuthExceptionHandler extends ExceptionHandler
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(Response::class);
        $this->logger = $container->get(LoggerFactory::class)->make('auth', 'auth');
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof UnauthorizedException) {
            $this->stopPropagation();

            return $this->response->fail(AppCode::AUTH_FAIL);
        }

        if ($throwable instanceof AuthException) {
            $this->stopPropagation();
            $this->logger->info(format_throwable($throwable));

            return $this->response->fail(AppCode::AUTH_EXCEPTION, [], $throwable->getMessage());
        }

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof AuthException;
    }
}
