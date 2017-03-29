<?php

/**
 * elastic 应用端 请求内部请求模块
 *
 */
namespace chenyuanqi\elasticSearchService;

use Config;
use Elasticsearch\ClientBuilder;

class Builder
{
    /**
     * elastic客户端连接
     */
    protected $client;

    /**
     * 索引库名
     */
    protected $index;

    /**
     * 索引类型
     */
    protected $type;

    /**
     * elastic 配置及索引配置
     */
    public $config, $indexConfig;

    public function __construct()
    {
        $hosts        = Config::get('essearch.hosts', []);
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    /**
     * 设置 elastic 链接
     *
     * @param string $configName 配置名
     *
     * @return void
     */
    protected function setConfig($configName)
    {
        $this->config      = Config::get('elasticsearch.'.$configName, []);
        $this->indexConfig = $this->config['index'];
        $this->index       = $this->indexConfig['indices'];
        $this->type        = $this->indexConfig['type'];
    }
}
