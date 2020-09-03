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

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;

trait EsModel
{
    /**
     * @Inject
     *
     * @var ElasticsearchClient
     */
    public $es;

    public $esIndexSetting;

    protected $esIndex;

    public function search($body, $config = [])
    {
        return $this->es->search($this->esIndexName(), $body, $config);
    }

    public function esIndexName()
    {
        if (!$this->esIndex) {
            return Str::snake($this->getTable());
        }

        return $this->esIndex;
    }

    public function searchScroll($body, $scroll, $scrollSize = 20, $config = [])
    {
        return $this->es->searchScroll($this->esIndexName(), $body, $scroll, $scrollSize, $config);
    }

    public function esInsertBath($data, $hidden = [])
    {
        foreach ($data as $index => $value) {
            $data[$index] = $this->hiddenData($value, $hidden);
        }

        return $this->es->dataBathInsert($this->esIndexName(), $data);
    }

    public function esCreateIndex($shards = null, $replicas = null, $reload = false)
    {
        $params = [];

        $indexName = $this->esIndexName();
        if ($reload) {
            $status = $this->es->indexExists($indexName);
            if ($status) {
                $this->es->indexDelete($indexName);
            }
        }

        $settings = $this->esGetIndexSettings();
        if (!is_null($settings)) {
            $params['settings'] = $settings;
        }

        if (!is_null($shards)) {
            $params['settings']['number_of_shards'] = $shards;
        }

        if (!is_null($replicas)) {
            $params['settings']['number_of_replicas'] = $replicas;
        }

        $mappingProperties = $this->esIndexMapping;
        if (!is_null($mappingProperties)) {
            $params['mappings'] = [
                '_source' => ['enabled' => true],
                'properties' => $mappingProperties,
            ];
        }

        return $this->es->indexCreate($indexName, $params);
    }

    public function esGetIndexSettings()
    {
        if (!empty($this->esIndexSetting)) {
            return $this->esIndexSetting;
        }

        return [];
    }

    public function esInsert($hidden = [])
    {
        return $this->es->dataInsert($this->esIndexName(), $this->esGetIndexData($hidden));
    }

    public function esGetIndexData($hidden = [])
    {
        if (!$this->exists) {
            return false;
        }

        $data = $this->toArray();

        return $this->hiddenData($data, $hidden);
    }

    private function hiddenData($data, $hidden)
    {
        $hidden = array_merge($this->getHidden(), $hidden);
        $dataKeys = array_keys($data);
        if (!empty($hidden)) {
            foreach ($dataKeys as $dataKey) {
                if (in_array($dataKey, $hidden)) {
                    unset($data[$dataKey]);
                }
            }
        }

        return $data;
    }
}
