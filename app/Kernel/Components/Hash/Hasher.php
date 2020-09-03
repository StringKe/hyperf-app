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
namespace App\Kernel\Components\Hash;

interface Hasher
{
    /**
     * Get information about the given hashed value.
     *
     * @param string $hashedValue
     *
     * @return array
     */
    public function info($hashedValue);

    /**
     * Hash the given value.
     *
     * @param string $value
     *
     * @return string
     */
    public function make($value, array $options = []);

    /**
     * Check the given plain value against a hash.
     *
     * @param string $value
     * @param string $hashedValue
     *
     * @return bool
     */
    public function check($value, $hashedValue, array $options = []);

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param string $hashedValue
     *
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = []);
}
