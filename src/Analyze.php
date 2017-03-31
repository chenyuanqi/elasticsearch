<?php

namespace chenyuanqi\elasticSearchService;

class Analyze extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 调用 ik 分词器
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

        $result = $this->getClient()->indices()->analyze([
            'index'    => $this->index,
            'analyzer' => $analyzer,
            'text'     => $word
        ]);

        return $result['tokens']['token'] ? : [];
    }
}
