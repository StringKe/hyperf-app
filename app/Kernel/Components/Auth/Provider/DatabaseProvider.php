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
use App\Kernel\Components\Auth\GenericUser;
use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;

class DatabaseProvider extends AbstractUserProvider
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var mixed
     */
    private $conn;

    public function __construct(ContainerInterface $container, ConfigInterface $appConfig, $name, $config)
    {
        parent::__construct($container, $appConfig, $name, $config);

        $this->conn = isset($config['connection']) ? $config['connection'] : 'default';

        if (isset($config['table'])) {
            $this->table = $config['table'];
        } else {
            throw  new UserProviderException('没有配置数据表');
        }
    }

    public function retrieveById($identifier)
    {
        $user = $this->query()->find($identifier);

        return $this->getGenericUser($user);
    }

    public function query()
    {
        return Db::connection($this->conn)->table($this->table);
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (1 === count($credentials) &&
                array_key_exists('password', $credentials))) {
            return null;
        }

        $query = $this->query();

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

        $user = $query->first();

        return $this->getGenericUser($user);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check(
            $credentials['password'],
            $user->getAuthToken()
        );
    }

    protected function getGenericUser($user)
    {
        if (!is_null($user)) {
            return new GenericUser((array) $user);
        }

        return null;
    }
}
