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

use Carbon\Carbon;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;
use Hyperf\Utils\Arr;
use Psr\Container\ContainerInterface;

class DeviceManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Redis
     */
    protected $storage;

    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct()
    {
        $this->container = di();
        $this->storage = $this->container->get(Redis::class);
        $this->request = $this->container->get(RequestInterface::class);
    }

    public function count($userId)
    {
        if ($all = $this->getAllDeviceByUserId($userId)) {
            return isset($all['tokens']) ? count($all['tokens']) : 0;
        }

        return 0;
    }

    public function getAllDeviceByUserId($userId)
    {
        $userId = (int) $userId;
        $allDeviceToken = $this->storage->sMembers("device:user:{$userId}");

        if (!empty($allDevice)) {
            return null;
        }
        $allDevice = [
            'tokens' => [],
            'infos' => [],
        ];
        foreach ($allDeviceToken as $token) {
            $info = $this->storage->get("device:token:{$token}");
            if ($info) {
                $allDevice['infos'][$token] = $info;
                $allDevice['tokens'][] = $token;
            } else {
                $this->storage->sRem("device:user:{$userId}", $token);
                $this->storage->del("device:token:{$token}");
                $this->storage->hDel('device:manager', $token);
            }
        }

        return $allDevice;
    }

    public function getDeviceByToken($token)
    {
        $token = (string) $token;

        $info = $this->storage->get("device:token:{$token}");
        if ($info) {
            return $info;
        }
        $userId = $this->storage->hGet('device:manager', $token);
        $this->storage->sRem("device:user:{$userId}", $token);
        $this->storage->hDel('device:manager', $token);

        return null;
    }

    public function addDevice($userId, $token)
    {
        $token = (string) $token;
        $userId = (int) $userId;

        $expTime = 60 * 60 * 24 * 28;
        $data = [
            'user' => $userId,
            'time' => Carbon::now()->timestamp,
        ];
        // TODO 辨别当前环境是否 Command , 其余环境不存在 request

        if ($this->request) {
            $data = [
                'ip' => $this->getIp(),
                'ua' => $this->getUa(),
            ];
        }

        $this->storage->set("device:token:{$token}", $data, $expTime);
        $this->storage->hSet('device:manager', $token, $userId);
        $this->storage->sAdd("device:user:{$userId}", $token);
    }

    public function getIp()
    {
        return Arr::get($this->request->getServerParams(), 'remote_addr', 'unknown');
    }

    public function getUa()
    {
        return Arr::first(Arr::get($this->request->getHeaders(), 'user-agent', 'unknown'));
    }

    public function forgetDevice($userId, $token)
    {
        $this->storage->sRem("device:user:{$userId}", $token);
        $this->storage->del("device:token:{$token}");
        $this->storage->hDel('device:manager', $token);
    }

    public function getUserIdByToken($token)
    {
        $info = $this->storage->get("device:token:{$token}");

        if ($info && isset($info['user'])) {
            return $info['user'];
        }

        return null;
    }
}
