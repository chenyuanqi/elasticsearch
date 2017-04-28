<?php

return [
    // 调试模式
    'debug_mode'    => true,

    // 默认索引 (相当于数据库的名称)
    'default_index' => 'default',
    // 默认类型 (相当于数据表的名称)
    'default_type'  => 'default',

    // 模板配置
    'default_template' => [
        'order'    => 1,
        'template' => 'cool*',
        'settings' => [
            'number_of_shards'   => 1,
            'number_of_replicas' => 0,
        ],
        'mappings'  => [
            'type_name' => [
                '_all'       => [
                    'enabled' => false,
                ],
                'properties' => [
                    '@timestamp' => [
                        'type'   => 'date',
                        'format' => 'dd/MMM/YYYY:HH:mm:ss Z',
                    ],
                ],
            ],
        ],
    ],

    // 索引配置, 索引名称 default
    'default'       => [
        // 链接配置 (可配多个)
        'connection' => [
            'http://es_admin:es_admin_password@127.0.0.1:9200',
        ],

        'alias'      =>  'df_v1',

        // 索引初始配置
        'settings'   => [
            // 分片与副本
            'number_of_shards'   => 3,
            'number_of_replicas' => 1,

            'analysis' => [
                'analyzer'    => [
                    // 智能拼音分析
                    'ik_letter_smart'    => [
                        'type'        => 'custom',
                        'tokenizer'   => 'ik_max_word',
                        'filter'      => ['lc_first_letter']
                    ],
                    'ik_pinyin_smart'    => [
                        'type'        => 'custom',
                        'tokenizer'   => 'ik_max_word',
                        'filter'      => ['lowercase']
                    ],
                    // 同义词分析器
                    'synonym_analyzer'   => [
                        'tokenizer'   => 'whitespace',
                        'filter'      => ['local_synonym'],
                        'char_filter' => ['special_char_convert']
                    ]
                ],
                'filter'      => [
                    'local_synonym'  => [
                        // 同义词类型
                        'type'          => 'dynamic_synonym',
                        // 同义词文件路径，本地使用相对路径(/etc/elasticsearch)
                        'synonyms_path' => 'pool_synonym.txt',
                        // 检测文件更新间隔时间，默认 60 秒
                        'interval'      => 30
                    ],
                    'remote_synonym' => [
                        'type'          => 'dynamic_synonym',
                        // 同义词文件路径，远程使用 http 链接 (测试链接需保证可访问，否则留空)
                        'synonyms_path' => '',
                        // 检测文件更新间隔时间，默认 60 秒
                        'interval'      => 60
                    ]
                ],
                'char_filter' => [
                    // 特殊字符过滤
                    'special_char_convert' => [
                        'type'          => 'mapping',
                        // 映射字符，或可使用映射文件 mappings_path 并存储在路径(/etc/elasticsearch)
                        'mappings'      => [
                            '& => ',
                            '* => '
                        ]
                    ],
                ],
            ]
        ],

        // 索引下的类型配置
        'indices'    => [
            // 类型名称 default
            'default' => [
                // model 读取, 使用批量处理数据时需要配置
                'model'    => '\App\Model\Default',

                // 批量处理数据时，限制单次处理数量
                'limit'    => 10000,

                // 索引字段
                'fields'   => [
                    'title',
                    'content',
                    'score'
                ],

                // 映射配置
                'mappings' => [
                    'default' => [
                        // match _all 匹配解析器
                        '_all'       => [
                            'enabled'  => true,
                            'analyzer' => 'ik_smart'
                        ],
                        'properties' => [
                            'title' => [
                                // 类型 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html]
                                'type'           => 'string',
                                // 是否使用解析器索引，如不适用解析器，值为 not_analyzed [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-index.html]
                                'index'          => 'analyzed',
                                // 提升得分 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-boost.html]
                                'boost'          => 10,
                                // 解析条款 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/term-vector.html]
                                'term_vector'    => 'with_positions_offsets',
                                // 解析器
                                'analyzer'       => 'ik_smart',
                                // 控制如上 match _all 字段 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/include-in-all.html]
                                'include_in_all' => true,
                                // 同一字段的不同表现：排序、聚合等 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/multi-fields.html]
                                'fields'         => [
                                    'synonym'      => [
                                        'type'        => 'string',
                                        'boost'       => 1,
                                        'term_vector' => 'with_positions_offsets',
                                        'analyzer'    => 'synonym_analyzer',
                                    ],
                                    'smart_pinyin' => [
                                        'type'            => 'string',
                                        'boost'           => 1,
                                        'term_vector'     => 'with_positions_offsets',
                                        'analyzer'        => 'lc_index',
                                        'search_analyzer' => 'lc_search'
                                    ],
                                    'convert' => [
                                        'type'            => 'string',
                                        'boost'           => 1,
                                        'term_vector'     => 'with_positions_offsets',
                                        'analyzer'        => 'tsconvert_keep_both'
                                    ],
                                    'ext'          => [
                                        'type'        => 'string',
                                        'boost'       => 1,
                                        'term_vector' => 'with_positions_offsets',
                                        'analyzer'    => 'standard',
                                    ],
                                ],
                            ],
                            'content'        => [
                                'type'           => 'string',
                                'boost'          => 5,
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
                            'score'             => [
                                'type'             => 'long'
                            ],
                        ]
                    ]
                ],
            ]
        ],
    ],

    // 索引配置, 索引名称 default
    'default_migrate'       => [
        // 链接配置 (可配多个)
        'connection' => [
            'http://es_admin:es_admin_password@127.0.0.1:9200',
        ],

        // 别名配置
        'alias'      =>  'df_v2',

        // 索引初始配置
        'settings'   => [
            // 分片与副本
            'number_of_shards'   => 3,
            'number_of_replicas' => 1,

            'analysis' => [
                'analyzer'    => [
                    // 智能拼音分析
                    'ik_letter_smart'    => [
                        'type'        => 'custom',
                        'tokenizer'   => 'ik_max_word',
                        'filter'      => ['lc_first_letter']
                    ],
                    'ik_pinyin_smart'    => [
                        'type'        => 'custom',
                        'tokenizer'   => 'ik_max_word',
                        'filter'      => ['lowercase']
                    ],
                    // 同义词分析器
                    'synonym_analyzer'   => [
                        'tokenizer'   => 'whitespace',
                        'filter'      => ['local_synonym'],
                        'char_filter' => ['special_char_convert']
                    ]
                ],
                'filter'      => [
                    'local_synonym'  => [
                        // 同义词类型
                        'type'          => 'dynamic_synonym',
                        // 同义词文件路径，本地使用相对路径(/etc/elasticsearch)
                        'synonyms_path' => 'pool_synonym.txt',
                        // 检测文件更新间隔时间，默认 60 秒
                        'interval'      => 30
                    ],
                    'remote_synonym' => [
                        'type'          => 'dynamic_synonym',
                        // 同义词文件路径，远程使用 http 链接 (测试链接需保证可访问，否则留空)
                        'synonyms_path' => '',
                        // 检测文件更新间隔时间，默认 60 秒
                        'interval'      => 60
                    ]
                ],
                'char_filter' => [
                    // 特殊字符过滤
                    'special_char_convert' => [
                        'type'          => 'mapping',
                        // 映射字符，或可使用映射文件 mappings_path 并存储在路径(/etc/elasticsearch)
                        'mappings'      => [
                            '& => ',
                            '* => '
                        ]
                    ],
                ],
            ]
        ],

        // 索引下的类型配置
        'indices'    => [
            // 类型名称 default
            'default_index' => [
                // model 读取, 使用批量处理数据时需要配置
                'model'    => '\App\Model\Default',

                // 批量处理数据时，限制单次处理数量
                'limit'    => 10000,

                // 索引字段
                'fields'   => [
                    'title',
                    'content',
                    'score'
                ],

                // 映射配置
                'mappings' => [
                    'default' => [
                        // match _all 匹配解析器
                        '_all'       => [
                            'enabled'  => true,
                            'analyzer' => 'ik_smart'
                        ],
                        'properties' => [
                            'title' => [
                                // 类型 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html]
                                'type'           => 'string',
                                // 是否使用解析器索引，如不适用解析器，值为 not_analyzed [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-index.html]
                                'index'          => 'analyzed',
                                // 提升得分 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-boost.html]
                                'boost'          => 10,
                                // 解析条款 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/term-vector.html]
                                'term_vector'    => 'with_positions_offsets',
                                // 解析器
                                'analyzer'       => 'ik_smart',
                                // 控制如上 match _all 字段 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/include-in-all.html]
                                'include_in_all' => true,
                                // 同一字段的不同表现：排序、聚合等 [参考 https://www.elastic.co/guide/en/elasticsearch/reference/current/multi-fields.html]
                                'fields'         => [
                                    'synonym'      => [
                                        'type'        => 'string',
                                        'boost'       => 1,
                                        'term_vector' => 'with_positions_offsets',
                                        'analyzer'    => 'synonym_analyzer',
                                    ],
                                    'smart_pinyin' => [
                                        'type'            => 'string',
                                        'boost'           => 1,
                                        'term_vector'     => 'with_positions_offsets',
                                        'analyzer'        => 'lc_index',
                                        'search_analyzer' => 'lc_search'
                                    ],
                                    'convert' => [
                                        'type'            => 'string',
                                        'boost'           => 1,
                                        'term_vector'     => 'with_positions_offsets',
                                        'analyzer'        => 'tsconvert_keep_both'
                                    ]
                                ],
                            ],
                            'content'        => [
                                'type'           => 'string',
                                'boost'          => 5,
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
                            'score'             => [
                                'type'             => 'long'
                            ],
                        ]
                    ]
                ],
            ]
        ],
    ],

];
