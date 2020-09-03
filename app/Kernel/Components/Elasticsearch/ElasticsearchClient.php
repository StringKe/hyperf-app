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

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Hyperf\Guzzle\RingPHP\PoolHandler;
use Hyperf\Utils\Arr;
use Swoole\Coroutine;

class ElasticsearchClient
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $builder = ClientBuilder::create();
        if (Coroutine::getCid() > 0) {
            $handler = make(PoolHandler::class, [
                'option' => [
                    'max_connections' => 50 * swoole_cpu_num(),
                ],
            ]);
            $builder->setHandler($handler);
        }

        $config = config('elasticsearch');
        $this->client = $builder->setHosts($config)->build();
    }

    public function esClient()
    {
        return $this->client;
    }

    public function indexExists($indexName): bool
    {
        $params = [
            'index' => $indexName,
        ];

        return $this->client->indices()->exists($params);
    }

    public function indexCreate($indexName, $indexConfig): bool
    {
        $params = $this->esBody($indexName, $indexConfig);
        $response = $this->client->indices()->create($params);

        return $response['acknowledged'];
    }

    public function esBody($index, $body = [], $merge = [])
    {
        $params = [
            'index' => $index,
        ];
        if (!empty($body)) {
            $params = array_merge($params, ['body' => $body]);
        }

        return array_merge($params, $merge);
    }

    public function indexDelete($indexName)
    {
        $params = $this->esBody($indexName);

        return $this->client->indices()->delete($params);
    }

    public function dataInsert($indexName, $data, $id = null)
    {
        $params = $this->esBody($indexName, $data);
        if (null === $id) {
            $id = Arr::get($data, 'id', null);
        }

        if ($id) {
            $params['id'] = $id;
        }

        return $this->client->index($params);
    }

    public function dataBathInsert($indexName, $bathData = [])
    {
        if (empty($bathData)) {
            return false;
        }
        $queue = [];
        foreach ($bathData as $data) {
            $id = Arr::get($data, 'id', null);
            $config = [
                '_index' => $indexName,
                '_type' => '_doc',
            ];

            $queue[] = [
                'index' => $id ? array_merge($config, ['_id' => $id]) : $config,
            ];
            $queue[] = $data;
        }

        return $this->client->bulk(['body' => $queue]);
    }

    public function search($indexName, $body = [], $config = [])
    {
        $params = [
            'index' => $indexName,
            'body' => $body,
        ];

        return $this->client->search(array_merge($params, $config));
    }

    public function searchScroll($indexName, $body = [], $scrollId = null, $scrollSize = 20, $config = [])
    {
        if (null === $scrollId) {
            $params = [
                'scroll' => '1m',
                'size' => $scrollSize,
                'index' => $indexName,
                'body' => $body,
            ];

            return $this->client->search($params);
        }
        $params = [
            'body' => [
                'scroll_id' => $scrollId,
                'scroll' => '1m',
            ],
        ];

        return $this->client->scroll($params);
    }
}
