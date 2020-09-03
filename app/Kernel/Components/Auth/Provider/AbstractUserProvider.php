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
namespace App\Kernel\Components\Auth\Provider;

use App\Kernel\Components\Auth\Contracts\UserProvider;
use App\Kernel\Components\Hash\HashManager;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractUserProvider implements UserProvider
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    protected $appConfig;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var HashManager
     */
    protected $hasher;

    public function __construct(ContainerInterface $container, ConfigInterface $appConfig, $name, $config)
    {
        $this->container = $container;
        $this->appConfig = $appConfig;
        $this->name = $name;
        $this->config = $config;
        $this->hasher = $this->container->get(HashManager::class);
    }
}
