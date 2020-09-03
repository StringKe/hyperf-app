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
namespace App\Kernel\Components;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\PoolHandler;
use Hyperf\Guzzle\RetryMiddleware;
use Hyperf\Utils\Coroutine;

class HttpClient
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct($config = [])
    {
        $handler = null;
        if (Coroutine::inCoroutine()) {
            $handler = make(PoolHandler::class, [
                'option' => [
                    'max_connections' => 10000,
                ],
            ]);
        }

        $retry = make(RetryMiddleware::class, [
            'retries' => 3,
            'delay' => 5,
        ]);

        $stack = HandlerStack::create($handler);
        $stack->push($retry->getMiddleware(), 'retry');

        $config = [
            'config' => array_merge([
                'timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.16 Safari/537.36',
                ],
            ], $config),
        ];

        $config = array_merge([
            'config' => [
                'handler' => $stack,
            ],
        ], $config);

        $this->client = make(Client::class, $config);
    }

    public function client()
    {
        return $this->client;
    }

    public function get(string $uri, array $options = [])
    {
        $response = $this->client->get($uri, $options);
        if (200 === $response->getStatusCode()) {
            return $response;
        }

        return null;
    }

    public function post(string $uri, array $options = [])
    {
        $response = $this->client->post($uri, $options);
        if (200 === $response->getStatusCode()) {
            return $response;
        }

        return null;
    }
}
