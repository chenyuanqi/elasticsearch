<?php

return [
    // 默认索引
    'default_index' => 'default',

    // 测试索引配置
    'default' => [
        // Elasticsearch host 及 port
        'host'     => [
            '127.0.0.1:9200'
        ],

        // model 读取, 使用批量处理数据时需要配置
        'model'    => '\App\Model\Default',
        // 批量处理数据时，限制单次数量
        'limit'    => 10000,
        // 索引名称和类型
        'index'    => [
            'indices' => 'default', // 索引名称
            'type'    => 'default', // 索引类型
            'fields'  => [          // 索引字段
                'title'
            ]
        ],
        // 映射配置
        'mappings' => [
            'default' => [
                // match _all 匹配解析器
                '_all'       => [
                    'analyzer' => 'ik_smart'
                ],
                'properties' => [
                    'name' => [
                        // 类型 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html]
                        'type'           => 'string',
                        // 提升得分 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-boost.html]
                        'boost'          => 10,
                        // 解析条款 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/term-vector.html]
                        'term_vector'    => 'with_positions_offsets',
                        'analyzer'       => 'ik_smart',
                        // 控制如上 match _all 字段 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/include-in-all.html]
                        'include_in_all' => true,
                        // 同一字段的不同表现：排序、聚合等 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/multi-fields.html]
                        'fields'         => [
                            'ext' => [
                                'type'        => 'string',
                                'boost'       => 1,
                                'term_vector' => 'with_positions_offsets',
                                'analyzer'    => 'standard',
                            ],
                        ],
                    ],
                ]
            ]
        ],

    ],

];
