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
namespace Hyperf\Paginator\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Paginator\Paginator;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Psr\Http\Message\ServerRequestInterface;

class PageResolverListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event)
    {
        Paginator::currentPageResolver($this->dispatch('page', 1, 1));
        Paginator::currentPageSizeResolver($this->dispatch('page_size', 20, 10));
    }

    public function dispatch($attrName, $defaultValue, $minValue)
    {
        return function ($pageName, $min = null) use ($attrName, $defaultValue, $minValue) {
            if (!$pageName) {
                $pageName = $attrName;
            }
            if (!$min) {
                $min = $minValue;
            }
            if (!ApplicationContext::hasContainer() ||
                !interface_exists(RequestInterface::class) ||
                !Context::has(ServerRequestInterface::class)
            ) {
                return $defaultValue;
            }

            $container = ApplicationContext::getContainer();
            $value = $container->get(RequestInterface::class)->input($pageName);

            if (false !== filter_var($value, FILTER_VALIDATE_INT) && (int) $value >= $min) {
                return (int) $value;
            }

            return $defaultValue;
        };
    }
}
