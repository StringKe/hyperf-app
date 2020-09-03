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
namespace App\Kernel\Components\Auth;

use App\Kernel\Components\Auth\Contracts\Authenticatable;
use App\Kernel\Components\Auth\Contracts\Guard;
use App\Kernel\Components\Auth\Contracts\UserProvider;
use App\Kernel\Components\Auth\Exception\GuardException;
use App\Kernel\Components\Auth\Exception\UserProviderException;
use App\Kernel\Components\Auth\Guard\AbstractAuthGuard;
use App\Model\User;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AuthManager.
 *
 * @method null|Authenticatable|User user()
 * @method bool                      validate(array $credentials = [])
 * @mixin AbstractAuthGuard
 */
class AuthManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface|mixed
     */
    protected $config;

    /**
     * @var array
     */
    protected $guards = [];

    /**
     * @var array
     */
    protected $providers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);
    }

    public function __call($name, $arguments)
    {
        $guard = $this->guard();

        if (method_exists($guard, $name)) {
            return call_user_func_array([$guard, $name], $arguments);
        }

        throw new GuardException('Method not defined. method:'.$name);
    }

    public function guard(?string $name = null): Guard
    {
        $name = $name ?? $this->defaultGuard();

        if (null === $name || empty($this->config->get("auth.guards.{$name}"))) {
            throw new GuardException("Does not support this driver: {$name}");
        }

        $config = $this->config->get("auth.guards.{$name}");
        $userProvider = $this->provider($config['provider']);

        return $this->guards[$name] ?? $this->guards[$name] = make(
            $config['driver'],
            [
                'container' => $this->container,
                'appConfig' => $this->config,
                'name' => $name,
                'config' => $config,
                'provider' => $userProvider,
            ]
        );
    }

    public function defaultGuard()
    {
        return $this->config->get('auth.defaults.guard', null);
    }

    public function provider(?string $name = null): UserProvider
    {
        $name = $name ?? $this->defaultProvider();

        if (null === $name || empty($this->config->get("auth.providers.{$name}"))) {
            throw new UserProviderException("Does not support this provider: {$name}");
        }

        $config = $this->config->get("auth.providers.{$name}");

        return $this->providers[$name] ?? $this->providers[$name] = make(
            $config['driver'],
            [
                'container' => $this->container,
                'appConfig' => $this->config,
                'name' => $name,
                'config' => $config,
            ]
        );
    }

    public function defaultProvider()
    {
        return $this->config->get('auth.defaults.provider', null);
    }

    public function getGuards(): array
    {
        return $this->guards;
    }
}
