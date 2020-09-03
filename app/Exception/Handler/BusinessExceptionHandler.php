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
namespace App\Exception\Handler;

use App\Exception\BusinessException;
use App\Kernel\Http\Response;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class BusinessExceptionHandler extends ExceptionHandler
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
     * @var StdoutLoggerInterface;
     */
    protected $stdoutLogger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(Response::class);
        if ('dev' === config('app_env')) {
            $this->stdoutLogger = $container->get(StdoutLoggerInterface::class);
        }
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $logger = $this->container->get(LoggerFactory::class)->get('system', 'system');
        if ($throwable instanceof BusinessException) {
            $logger = $this->container->get(LoggerFactory::class)->get('app', 'default');

            $logger->warning(format_throwable($throwable));

            return $this->response->fail($throwable->getCode(), [], $throwable->getMessage());
        }

        if ('dev' === config('app_env')) {
            $this->stdoutLogger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
            $this->stdoutLogger->error($throwable->getTraceAsString());
        }

        $logger->error(format_throwable($throwable));

        return $this->response->fail($throwable->getCode(), [], '服务器无法处理，遇到错误，请稍后重试。');
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
