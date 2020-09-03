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
namespace App\Kernel\Log;

use Hyperf\Utils\Context;
use Monolog\Processor\ProcessorInterface;

class AppendRequestIdProcessor implements ProcessorInterface
{
    const REQUEST_ID = 'request_id';

    public function __invoke(array $records)
    {
        $records['context']['request_id'] = Context::getOrSet(self::REQUEST_ID, md5(uniqid()));

        return $records;
    }
}
