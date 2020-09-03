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

use App\Kernel\Components\Auth\Contracts\Authenticatable;
use App\Kernel\Components\Auth\Exception\UserProviderException;
use App\Model\Model;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;

class EloquentProvider extends AbstractUserProvider
{
    protected $model;

    public function __construct(ContainerInterface $container, ConfigInterface $appConfig, $name, $config)
    {
        parent::__construct($container, $appConfig, $name, $config);

        if (isset($config['model'])) {
            $this->model = $config['model'];
        } else {
            throw new UserProviderException('没有配置模型');
        }
    }

    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first()
        ;
    }

    /**
     * @return Authenticatable|Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || (1 === count($credentials) && Str::contains($this->firstCredentialKey($credentials), 'password'))) {
            return null;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthToken());
    }

    protected function newModelQuery($model = null)
    {
        return is_null($model)
            ? $this->createModel()->newQuery()
            : $model->newQuery();
    }

    protected function firstCredentialKey(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }

        return null;
    }
}
