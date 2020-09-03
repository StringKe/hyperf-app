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
namespace App\Constants;

use Hyperf\Constants\ConstantsCollector;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Utils\ApplicationContext;

abstract class CodeMessage
{
    /**
     * 根据定义的常量以及 PHPDoc 获取对应 Message.
     *
     * 常量需要包含 (@)Message("xxxx") 的 PHPDoc
     *
     * @param int $code 状态码
     *
     * @return mixed|string message或翻译key
     */
    public static function getMessage(int $code = 1000)
    {
        $class = get_called_class();
        $message = ConstantsCollector::getValue($class, $code, 'message');
        $result = self::translate($message, [$code]);

        if ($result && $result !== $message) {
            return $result;
        }

        $count = count([$code]);
        if ($count > 0) {
            return sprintf($message, ...(array) $code);
        }

        return $message;
    }

    /**
     * 获取对应的翻译字符.
     *
     * @param $key string 翻译Key
     * @param $arguments array 参数
     *
     * @return null|string 对应字符
     */
    protected static function translate($key, $arguments): ?string
    {
        if (!ApplicationContext::hasContainer() || !ApplicationContext::getContainer()->has(TranslatorInterface::class)) {
            return null;
        }

        $replace = $arguments[0] ?? [];
        if (!is_array($replace)) {
            return null;
        }

        $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);

        return $translator->trans($key, $replace);
    }
}
