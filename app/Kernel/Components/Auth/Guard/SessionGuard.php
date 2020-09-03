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
use App\Kernel\Components\Auth\Contracts\Authenticatable;
use App\Kernel\Components\Auth\Contracts\UserProvider;
use App\Kernel\Components\Auth\Exception\DeviceException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Psr\Container\ContainerInterface;

class SessionGuard extends AbstractAuthGuard
{
    /**
     * @var mixed|SessionInterface
     */
    protected $session;

    public function __construct(ContainerInterface $container, ConfigInterface $appConfig, $name, $config, UserProvider $provider)
    {
        parent::__construct($container, $appConfig, $name, $config, $provider);
        $this->session = $this->container->get(SessionInterface::class);
    }

    /**
     * @param null $token
     *
     * @return bool
     */
    public function login(Authenticatable $user, $token = null)
    {
        if (!$this->canLogin($user->getAuthIdentifier())) {
            throw new DeviceException(AppCode::getMessage(AppCode::AUTH_DEVICE_MANY), AppCode::AUTH_DEVICE_MANY);
        }

        if (!$this->registerDevice($user, $this->session->getId())) {
            throw new DeviceException(AppCode::getMessage(AppCode::AUTH_DEVICE), AppCode::AUTH_DEVICE);
        }

        $this->session->put($this->sessionKey(), $user->getAuthIdentifier());
        $this->setUser($user);

        return true;
    }

    public function logout()
    {
        if ($this->session->has($this->sessionKey()) && $this->hasUser()) {
            $user = $this->getUser();
            $this->deviceManager->forgetDevice($user->getAuthIdentifier(), $this->session->get($this->sessionKey()));
            $this->session->remove($this->sessionKey());

            return true;
        }

        return false;
    }

    public function user()
    {
        if (!is_null($this->getUser())) {
            return $this->getUser();
        }

        if ($credentials = $this->session->get($this->sessionKey())) {
            $user = $this->provider->retrieveByCredentials($credentials);
            $this->setUser($user);

            return $user;
        }

        return null;
    }

    public function validate(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        $this->setUser($user);

        return !is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }

    protected function sessionKey(): string
    {
        return 'auth.'.$this->name;
    }
}
