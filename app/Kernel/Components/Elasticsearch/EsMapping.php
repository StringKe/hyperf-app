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
namespace App\Kernel\Components\Elasticsearch;

class EsMapping
{
    public static function ik($options = [])
    {
        $ik = self::text([
            'analyzer' => 'ik_max_word',
            'search_analyzer' => 'ik_smart',
        ]);

        return array_merge($ik, $options);
    }

    public static function text($options = [])
    {
        return self::type('text', $options);
    }

    public static function type($type = 'text', $options = [])
    {
        return array_merge([
            'type' => $type,
        ], $options);
    }

    public static function integer($options = [])
    {
        return self::type('integer', $options);
    }

    public static function date($options = [])
    {
        $options = array_merge([
            'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis',
        ], $options);

        return self::type('date', $options);
    }

    public static function boolean($options = [])
    {
        return self::type('boolean', $options);
    }

    public static function long($options = [])
    {
        return self::type('long', $options);
    }

    public static function keyword($options = [])
    {
        return self::type('keyword', $options);
    }

    public static function scaled_float($options = [])
    {
        $options = array_merge(['scaling_factor' => 100], $options);

        return self::type('scaled_float', $options);
    }
}
