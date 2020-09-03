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
namespace App\Kernel\Components\Resource;

use App\Kernel\Components\Resource\Contracts\PotentiallyMissing;
use Hyperf\Utils\Arr;

trait ConditionallyLoadsAttributes
{
    protected function filter($data)
    {
        $index = -1;

        foreach ($data as $key => $value) {
            ++$index;

            if (is_array($value)) {
                $data[$key] = $this->filter($value);

                continue;
            }

            if (is_numeric($key) && $value instanceof MergeValue) {
                return $this->mergeData(
                    $data,
                    $index,
                    $this->filter($value->data),
                    array_values($value->data) === $value->data
                );
            }

            if ($value instanceof self && is_null($value->resource)) {
                $data[$key] = null;
            }
        }

        return $this->removeMissingValues($data);
    }

    protected function mergeData($data, $index, $merge, $numericKeys)
    {
        if ($numericKeys) {
            return $this->removeMissingValues(array_merge(
                array_merge(array_slice($data, 0, $index, true), $merge),
                $this->filter(array_values(array_slice($data, $index + 1, null, true)))
            ));
        }

        return $this->removeMissingValues(array_slice($data, 0, $index, true) +
            $merge +
            $this->filter(array_slice($data, $index + 1, null, true)));
    }

    protected function removeMissingValues($data)
    {
        $numericKeys = true;

        foreach ($data as $key => $value) {
            if (($value instanceof PotentiallyMissing && $value->isMissing()) ||
                ($value instanceof self &&
                    $value->resource instanceof PotentiallyMissing &&
                    $value->isMissing())) {
                unset($data[$key]);
            } else {
                $numericKeys = $numericKeys && is_numeric($key);
            }
        }

        if (property_exists($this, 'preserveKeys') && true === $this->preserveKeys) {
            return $data;
        }

        return $numericKeys ? array_values($data) : $data;
    }

    protected function merge($value)
    {
        return $this->mergeWhen(true, $value);
    }

    protected function mergeWhen($condition, $value)
    {
        return $condition ? new MergeValue(value($value)) : new MissingValue();
    }

    protected function attributes($attributes)
    {
        return new MergeValue(
            Arr::only($this->resource->toArray(), $attributes)
        );
    }

    protected function whenLoaded($relationship, $value = null, $default = null)
    {
        if (func_num_args() < 3) {
            $default = new MissingValue();
        }

        if (!$this->resource->relationLoaded($relationship)) {
            return value($default);
        }

        if (1 === func_num_args()) {
            return $this->resource->{$relationship};
        }

        if (null === $this->resource->{$relationship}) {
            return;
        }

        return value($value);
    }

    protected function whenPivotLoaded($table, $value, $default = null)
    {
        return $this->whenPivotLoadedAs('pivot', ...func_get_args());
    }

    protected function whenPivotLoadedAs($accessor, $table, $value, $default = null)
    {
        if (3 === func_num_args()) {
            $default = new MissingValue();
        }

        return $this->when(
            $this->resource->{$accessor} &&
            ($this->resource->{$accessor} instanceof $table ||
                $this->resource->{$accessor}->getTable() === $table),
            ...[$value, $default]
        );
    }

    protected function when($condition, $value, $default = null)
    {
        if ($condition) {
            return value($value);
        }

        return 3 === func_num_args() ? value($default) : new MissingValue();
    }
}
