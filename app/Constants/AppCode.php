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

use Hyperf\Constants\Annotation\Constants;

/**
 * Class AppCode.
 *
 * @Constants
 */
class AppCode extends CodeMessage
{
    /**
     * @Message("成功")
     */
    const SUCCESS = 1000;

    /**
     * @Message("请求参数错误")
     */
    const REQUEST_VERIFY_ERROR = 1500;

    /**
     * @Message("请求错误")
     */
    const REQUEST_ERROR = 2000;

    /**
     * @Message("执行错误")
     */
    const EXECUTE_ERROR = 2010;

    /**
     * @Message("执行超时")
     */
    const EXECUTE_TIMEOUT = 2020;

    /**
     * @Message("登陆设备过多")
     */
    const AUTH_DEVICE_MANY = 1100;

    /**
     * @Message("暂时无法登陆此设备")
     */
    const AUTH_DEVICE = 1105;

    /**
     * @Message("登陆信息有误，请检查后重试")
     */
    const AUTH_FAIL = 1110;

    /**
     * @Message("登陆信息有误，请检查后重试")
     */
    const AUTH_EXCEPTION = 1120;

    /**
     * @Message("服务器暂时无法处理")
     */
    const SERVER_ERROR = 5000;

    /**
     * @Message("无法处理当前请求，请求路径不正确")
     */
    const ROUTE_ERROR = 5050;

    /**
     * @Message("无法处理当前请求，请求方法不正确")
     */
    const ROUTE_HANDEL_ERROR = 5055;
}
