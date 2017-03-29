<?php

/**
 * elastic 索引操作
 *
 */
namespace chenyuanqi\elasticSearchService;

use chenyuanqi\elasticSearchService\Builder;

class Index extends Builder
{
    public $indexConfig;
    public $index;
    public $type;

    public function __construct($indexName = "")
    {
        parent::__construct();
        $this->setConfig($indexName);
    }

    /**
     * 获得输入对象的需要的内容字段
     *
     * @param object $item 输入对象
     *
     * @return array
     */
    public function getItemBody($item)
    {
        $bodyConf = $this->indexConfig['fields'];
        $res      = [];
        foreach ($bodyConf as $key) {
            $res[$key] = isset($item->$key) ? $item->$key : null;
        }

        return $res;
    }

    /**
     * 获得单条索引
     *
     * @param object $item 输入对象
     *
     * @return object
     */
    public function get($item)
    {
        $id = $this->indexConfig['id'];
        try {
            $res = $this->client->get([
                'index' => $this->index,
                'type'  => $this->type,
                'id'    => $item->$id,
            ]);

            return $res;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return;
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * 添加单条索引
     *
     * @param object $item 输入对象
     *
     * @return void
     */
    public function created($item)
    {
        $id = $this->indexConfig['id'];
        $this->client->index([
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $item->$id,
            'body'  => $this->getItemBody($item)
        ]);
    }

    /**
     * 更新单条索引
     *
     * @param object $item 输入对象
     *
     * @return void
     */
    public function updated($item)
    {
        $id = $this->indexConfig['id'];
        $this->client->index([
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $item->$id,
            'body'  => $this->getItemBody($item)
        ]);
    }

    /**
     * 删除单条索引
     *
     * @param object $item 输入对象
     *
     * @return void
     */
    public function deleted($item)
    {
        $id = $this->indexConfig['id'];
        if ($this->get($item)) {
            $this->client->delete([
                'index' => $this->index,
                'type'  => $this->type,
                'id'    => $item->$id,
            ]);
        }
    }

    /**
     * 建立索引
     *
     * @return void
     */
    public function mapping()
    {
        $this->client->indices()->create([
            'index' => $this->index,
            'body'  => [
                'mappings' => $this->config['mappings']
            ]
        ]);
    }

    /**
     * 清空索引
     *
     * @return void
     */
    public function clear()
    {
        $this->client->indices()->delete([
            'index' => $this->index
        ]);
    }

    /**
     * 批量导入数据
     *
     * @param array $params 输入列表
     *
     * @return void
     */
    public function bulk($params)
    {
        if (!empty($params['body'])) {
            $this->client->bulk($params);
        }
    }
}
