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
use App\Kernel\Components\Auth\AuthManager;
use App\Kernel\Components\Auth\Contracts\Guard;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\JobInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

if (!function_exists('di')) {
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param null|string $id
     *
     * @return ContainerInterface|mixed
     */
    function di($id = null)
    {
        $container = ApplicationContext::getContainer();
        if ($id) {
            return $container->get($id);
        }

        return $container;
    }
}

if (!function_exists('format_throwable')) {
    /**
     * Format a throwable to string.
     */
    function format_throwable(Throwable $throwable): string
    {
        return di()->get(FormatterInterface::class)->format($throwable);
    }
}

if (!function_exists('queue_push')) {
    /**
     * Push a job to async queue.
     */
    function queue_push(JobInterface $job, int $delay = 0, string $key = 'default'): bool
    {
        $driver = di()->get(DriverFactory::class)->get($key);

        return $driver->push($job, $delay);
    }
}

if (!function_exists('auth')) {
    /**
     * 建议视图中使用该函数，其他地方请使用注入.
     *
     * @return AuthManager|Guard
     */
    function auth(?string $guard = null)
    {
        $auth = ApplicationContext::getContainer()->get(AuthManager::class);

        if (is_null($guard)) {
            return $auth;
        }

        return $auth->guard($guard);
    }
}

if (!function_exists('make_uuid')) {
    /**
     * 建议视图中使用该函数，其他地方请使用注入.
     *
     * @return string
     */
    function make_uuid()
    {
        $generator = ApplicationContext::getContainer()->get(IdGeneratorInterface::class);

        return $generator->generate();
    }
}
