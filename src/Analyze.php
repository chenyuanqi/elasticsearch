<?php

/**
 * elastic 分词调用
 *
 */
namespace chenyuanqi\elasticSearchService;

class Analyze extends Builder
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 调用分词器
     *
     * @param string $word     文本
     * @param string $analyzer 选择分析器, ik_smart 或 ik_max_words
     *
     * @return array
     */
    public function ikAnalyze($word, $analyzer = 'ik_smart')
    {
        if (!$word) {
            return [];
        }

        $result = $this->client->indices()->analyze([
            'index'    => $this->index,
            'analyzer' => $analyzer,
            'text'     => $word
        ]);

        return $result['tokens']['token'] ? : [];
    }
}
