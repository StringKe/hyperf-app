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
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;

class TokenGuard extends AbstractAuthGuard
{
    protected $request;

    protected $inputKey = 'api_token';

    public function __construct(ContainerInterface $container, ConfigInterface $appConfig, $name, $config, UserProvider $provider)
    {
        parent::__construct($container, $appConfig, $name, $config, $provider);
        $this->request = $this->container->get(RequestInterface::class);
        $this->inputKey = isset($config['input_key']) ? $config['input_key'] : 'api_token';
    }

    public function login(Authenticatable $user, $token)
    {
        if (!$this->canLogin($user->getAuthIdentifier())) {
            throw new DeviceException(AppCode::getMessage(AppCode::AUTH_DEVICE_MANY), AppCode::AUTH_DEVICE_MANY);
        }

        if (!$this->registerDevice($user, $token)) {
            throw new DeviceException(AppCode::getMessage(AppCode::AUTH_DEVICE), AppCode::AUTH_DEVICE);
        }
        $this->setUser($user);

        return true;
    }

    public function logout()
    {
        $token = $this->getTokenForRequest();
        if (!empty($token) && $this->hasUser()) {
            $user = $this->getUser();
            $this->deviceManager->forgetDevice($user->id, $token);

            return true;
        }

        return false;
    }

    public function getTokenForRequest()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        if (empty($token)) {
            $header = $this->request->header('Authorization', '');

            if (Str::startsWith($header, 'Bearer ')) {
                $token = Str::substr($header, 7);
            }
        }

        return $token;
    }

    public function user()
    {
        if (!is_null($this->getUser())) {
            return $this->getUser();
        }

        $user = null;
        $token = $this->getTokenForRequest();

        if (!empty($token)) {
            $userId = $this->deviceManager->getUserIdByToken($token);
            $user = $this->provider->retrieveById($userId);
        }

        $this->setUser($user);

        return $user;
    }

    public function validate(array $credentials = [])
    {
        if (!is_null($this->getUser())) {
            return $this->getUser();
        }

        $user = null;
        $token = $this->getTokenForRequest();

        if (!empty($token)) {
            $userId = $this->deviceManager->getUserIdByToken($token);
            if ($userId) {
                return true;
            }
        }

        return false;
    }
}
