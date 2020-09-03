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

use App\Kernel\Components\Auth\Contracts\UserProvider;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;

class ApiTokenGuard extends AbstractAuthGuard
{
    protected $request;

    protected $storageKey = 'api_token';

    protected $inputKey = 'api_token';

    public function __construct(ContainerInterface $container, ConfigInterface $appConfig, $name, $config, UserProvider $provider)
    {
        parent::__construct($container, $appConfig, $name, $config, $provider);
        $this->request = $this->container->get(RequestInterface::class);
        $this->storageKey = isset($config['storage_key']) ? $config['storage_key'] : 'api_token';
        $this->inputKey = isset($config['input_key']) ? $config['input_key'] : 'api_token';
    }

    public function user()
    {
        if (!is_null($this->getUser())) {
            return $this->getUser();
        }

        $user = null;
        $token = $this->getTokenForRequest();

        if (!empty($token)) {
            $user = $this->provider->retrieveByCredentials([
                $this->storageKey => $token,
            ]);
        }

        $this->setUser($user);

        return $user;
    }

    public function getTokenForRequest()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        return $token;
    }

    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        $credentials = [$this->storageKey => $credentials[$this->inputKey]];

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }
}
