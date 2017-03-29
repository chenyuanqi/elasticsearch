<?php

/**
 * elastic search 配置
 */
return [
    'hosts' => [
        '127.0.0.1:9200'
    ],

    // 测试索引配置
    'test'  => [
        // model 读取, 可不配置
        'model'    => '\App\Test',
        // 一次取的数量
        'limitNum' => 10000,
        // 索引名称和类型
        'index'    => [
            'indices' => 'test', // 索引库
            'type'    => 'test', // 索引类型
            'id'      => 'id',   // ID 来源
            'fields'  => [       // 索引字段
                                 'title'
            ]
        ],
        // 基础配置
        'mappings' => [
            'test' => [
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
