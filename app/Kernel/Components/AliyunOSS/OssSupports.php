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

class OssSupports
{
    private $flashData;

    public function getFlashData()
    {
        $flash = $this->flashData;
        $this->flashData = null;

        return $flash;
    }

    public function setFlashData($data = null)
    {
        $this->flashData = $data;
    }
}
