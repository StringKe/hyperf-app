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
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

Swoole\Runtime::enableCoroutine(true);

require BASE_PATH.'/vendor/autoload.php';

Hyperf\Di\ClassLoader::init();

$container = require BASE_PATH.'/config/container.php';

$container->get(Hyperf\Contract\ApplicationInterface::class);
