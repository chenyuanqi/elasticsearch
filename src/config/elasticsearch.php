<?php

/**
 * elastic search 配置
 */
return [
    // 默认索引
    'default_index' => 'default',

    // 测试索引配置
    'default' => [
        'host'     => '127.0.0.1:9200',

        // model 读取, 可不配置
        'model'    => '\App\Default',
        // 批量处理中，限制数量
        'limit'    => 10000,
        // 索引名称和类型
        'index'    => [
            'indices' => 'default', // 索引库
            'type'    => 'default', // 索引类型
            'id'      => 'id',      // ID 来源
            'fields'  => [          // 索引字段
                'title'
            ]
        ],
        // 基础配置
        'mappings' => [
            'default' => [
                '_all'       => [
                    'analyzer' => 'ik_smart'
                ],
                'properties' => [
                    'name' => [
                        'type'           => 'string',
                        'boost'          => 10,
                        'term_vector'    => 'with_positions_offsets',
                        'analyzer'       => 'ik_smart',
                        'include_in_all' => true,
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
