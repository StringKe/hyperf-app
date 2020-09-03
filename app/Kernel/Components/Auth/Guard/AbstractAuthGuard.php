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
namespace App\Kernel\Components\Auth\Guard;

use App\Constants\AppCode;
use App\Exception\BusinessException;
use App\Kernel\Components\Auth\Contracts\Authenticatable;
use App\Kernel\Components\Auth\Contracts\Guard;
use App\Kernel\Components\Auth\Contracts\UserProvider;
use App\Kernel\Components\Auth\DeviceManager;
use App\Kernel\Components\Auth\Exception\UnauthorizedException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;

abstract class AbstractAuthGuard implements Guard
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
     * @var UserProvider
     */
    protected $provider;

    /**
     * @var DeviceManager
     */
    protected $deviceManager;

    /**
     * AbstractAuthGuard constructor.
     *
     * @param $name
     * @param array $config
     */
    public function __construct(ContainerInterface $container, ConfigInterface $appConfig, $name, $config, UserProvider $provider)
    {
        $this->container = $container;
        $this->appConfig = $appConfig;
        $this->name = $name;
        $this->config = $config;
        $this->provider = $provider;
        $this->deviceManager = $this->container->get(DeviceManager::class);
    }

    public function hasUser()
    {
        return !is_null($this->user());
    }

    /**
     * @return $this
     */
    public function setUser(Authenticatable $user = null)
    {
        $name = 'auth.'.$this->name.'.user';
        Context::set($name, $user);

        return $this;
    }

    public function authenticate()
    {
        if (!is_null($user = $this->user())) {
            return $user;
        }

        throw new UnauthorizedException();
    }

    public function registerDevice(Authenticatable $user, $token)
    {
        if (!is_null($user)) {
            $userId = $user->getAuthIdentifier();
            $this->deviceManager->addDevice($userId, $token);

            return true;
        }

        return false;
    }

    public function canLogin($userId)
    {
        $allDevice = $this->deviceManager->count($userId);

        return $allDevice < 5;
    }

    public function guest()
    {
        return !$this->check();
    }

    public function check()
    {
        return !is_null($this->user());
    }

    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }

        return null;
    }

    public function getUser()
    {
        $name = 'auth.'.$this->name.'.user';
        if (Context::has($name)) {
            return Context::get($name);
        }

        return null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function attempt($credentials, $token)
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user, (string) $token);

            return true;
        }

        return false;
    }

    /**
     * 登陆.
     */
    public function login(Authenticatable $user, string $token)
    {
        throw new BusinessException(AppCode::ROUTE_ERROR, '必须实现 Guard 的 Login 方法');
    }

    protected function hasValidCredentials($user, $credentials)
    {
        return !is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }
}
