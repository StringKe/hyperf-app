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
namespace App\Kernel\Components\AliyunOSS;

use Exception;
use Hyperf\Filesystem\Contract\AdapterFactoryInterface;
use League\Flysystem\AdapterInterface;

class OssFactory implements AdapterFactoryInterface
{
    /**
     * @throws Exception
     */
    public function make(array $options): AdapterInterface
    {
        return new OssAdapter($options);
    }
}
