<?php

/**
 * eleastic 搜索调用
 *
 */
namespace chenyuanqi\elasticSearchService;

use Config;

class Search extends Builder
{
    /**
     * @param string $indexName 索引配置名
     */
    public function __construct($indexName = "")
    {
        parent::__construct();
        $this->setConfig($indexName);
    }

    /**
     * 搜索搜索调用
     *
     * @param string $word 输入查询词
     * @param int    $page 页码
     * @param int    $size 每页条数
     *
     * @return array
     */
    public function search($word, $page = 0, $size = 10)
    {
        if (!$word) {
            return [];
        }

        $from  = $page * $size;
        $items = $this->client->search([
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => [
                    'match' => [
                        '_all' => $word
                    ]
                ]
            ],
            'from'  => $from,
            'size'  => $size,
        ]);

        return $items;
    }

    /**
     * 多字段搜索搜索调用
     *
     * @param string $word   输入查询词
     * @param array  $fields 输入查询字段
     * @param int    $page   页码
     * @param int    $size   每页条数
     *
     * @return array
     */
    public function multiSearch($word, $fields, $page = 0, $size = 10)
    {
        if (!$word) {
            return [];
        }

        $from  = $page * $size;
        $items = $this->client->search([
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $word,
                        'type'   => 'most_fields',
                        'fields' => $fields,
                    ]
                ]
            ],
            'from'  => $from,
            'size'  => $size,
        ]);

        return $items;
    }

    /**
     * 格式化输出结果
     *
     * @param array $output 输出结果
     *
     * @return array
     */
    public function outputFormat($output)
    {
        if(!$output['hits']['total']) {
            return [];
        }

        // 格式化处理
        $result = collect($output['hits']['hits'])->map(function($item) {
            $item['_source']['_id'] = $item['_id'];
            return $item['_source'];
        })->toArray();
        // 总记录数
        $result['total'] = $output['hits']['total'];

        return $result;
    }
}
