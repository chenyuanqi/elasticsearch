<?php

/**
 * elastic 分词调用
 *
 */
namespace chenyuanqi\elasticSearchService;

use chenyuanqi\elasticSearchService\Builder;

class Analyze extends Builder
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 分词器调用
     *
     * @param string $text     输入文本
     * @param string $analyzer 选择分析器
     *
     * @return array
     */
    public function ikAnalyze($text, $index = 'index', $analyzer = 'ik_smart')
    {
        if (!$text) {
            return [];
        }
        $items = $this->client->indices()->analyze([
            'index'    => $index,
            'analyzer' => $analyzer,
            'text'     => $text
        ]);
        $res   = array_map(function ($item){
            return $item['token'];
        }, $items['tokens']);

        return $res;
    }
}
