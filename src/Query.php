<?php

namespace chenyuanqi\elasticsearch;

use Elasticsearch\ClientBuilder;

class Query
{
    /**
     * 客户端链接
     *
     * @var
     */
    protected static $client;

    /**
     * 索引配置
     *
     * @var
     */
    protected $config;

    /**
     * 索引名称
     *
     * @var
     */
    protected $index;

    /**
     * 类型名称
     *
     * @var
     */
    protected $type;

    /**
     * 查询条件
     *
     * @var
     */
    protected $where = [];

    /**
     * 查询结果
     *
     * @var
     */
    protected $output;

    /**
     * 查询字段
     *
     * @var array
     */
    protected $columns = ['*'];

    /**
     * 查询限制数量
     *
     * @var int
     */
    protected $limit = 10;

    /**
     * 查询偏移数量
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * 查询排序
     *
     * @var array
     */
    protected $order = [];

    /**
     * 获取配置，建立链接
     *
     * @param $index
     */
    public function __construct($index)
    {
        $config       = \Config::get('elasticsearch.'.$index, []);
        $this->config = $config;
        $this->index  = $config['index']['indices'];
        $this->type   = $config['index']['type'];

        if (!static::$client) {
            static::$client = ClientBuilder::create()->setHosts($config['host'])->build();
        }
    }

    /**
     * 获取链接 (外部使用)
     *
     * @return mixed
     */
    public function getClient()
    {
        return static::$client;
    }

    /**
     * 批量处理限制次数
     *
     * @return mixed
     */
    public function getLimitByConfig()
    {
        return array_get($this->config, 'limit', 1000);
    }

    /**
     * 对应 model 实例
     *
     * @return \stdClass
     */
    public function getModel()
    {
        if($model = array_get($this->config, 'model', '')) {
            return new $model;
        }

        return new \stdClass();
    }

    /**
     * 创建索引
     *
     * @return array
     */
    public function createIndex()
    {
        try {
            return self::$client->indices()->create([
                'index' => $this->index
            ]);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 输出调试信息
     */
    public function debug()
    {
        dd(self::$client->transport->getConnection()->getLastRequestInfo());
    }


    /**
     * 创建映射
     *
     * @return array
     */
    public function createMapping()
    {
        try {
            return self::$client->indices()->create([
                'index' => $this->index,
                'body'  => [
                    'mappings' => $this->config['mappings']
                ]
            ]);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 更新映射
     *
     * @return array
     */
    public function updateMapping()
    {
        try {
            return self::$client->indices()->putMapping([
                'index' => $this->index,
                'type'  => $this->type,
                'body'  => [
                    $this->config['mappings']
                ]
            ]);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 删除映射
     *
     * @return array
     */
    public function deleteMapping()
    {
        try {
            return self::$client->indices()->deleteMapping([
                'index' => $this->index,
                'type'  => $this->type,
            ]);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 新增数据
     *
     * @param $body
     * @param $id
     *
     * @return array
     */
    public function insert($body, $id = '')
    {
        try {
            $params = [
                'index' => $this->index,
                'type'  => $this->type,
                'body'  => $this->filter($body)
            ];
            $id && $params['id'] = $id;

            return self::$client->create($params);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 根据 ID 更新数据 (局部更新)
     *
     * @param array  $body
     * @param string $id
     *
     * @return array
     */
    public function updateById(array $body, $id)
    {
        try {
            $result = self::$client->update([
                'index' => $this->index,
                'type'  => $this->type,
                'id'    => $id,
                'body'  => [
                    'doc' => $this->filter($body)
                ]
            ]);

            return $result;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 根据查询条件更新数据
     *
     * @param array $body
     *
     * @return array
     */
    public function update(array $body)
    {
        if (!$this->where) {
            return [];
        }

        try {
            $result = self::$client->updateByQuery([
                'index'     => $this->index,
                'type'      => $this->type,
                'conflicts' => 'proceed',
                'body'      => $this->where
            ]);

            return $result;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 新增或更新数据
     *
     * @param        $body
     * @param string $id
     * @param int    $times 失败重试次数
     *
     * @return array
     */
    public function insertOrUpdate($body, $id = '', $times = 3)
    {
        try {
            $result = self::$client->update([
                'index' => $this->index,
                'type'  => $this->type,
                'id'    => $id,
                'body'  => [
                    'script' => 'ctx._source.counter += count',
                    'params' => [
                        'count' => $times
                    ],
                    'upsert' => $this->filter($body)
                    ]
            ]);

            return $result;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 根据 ID 删除数据
     *
     * @param $id
     *
     * @return array
     */
    public function deleteById($id)
    {
        try {
            $result = self::$client->delete([
                'index' => $this->index,
                'type'  => $this->type,
                'id'    => $id
            ]);

            return $result;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 根据查询条件删除数据
     *
     * @return array
     */
    public function delete()
    {
        if (!$this->where) {
            return [];
        }

        try {
            $result = self::$client->deleteByQuery([
                'index' => $this->index,
                'type'  => $this->type,
                'body'  => $this->where
            ]);

            return $result;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 删除并清空索引
     *
     * @return array
     */
    public function truncate()
    {
        try {
            return self::$client->indices()->delete([
                'index' => $this->index
            ]);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 批量增删改
     *
     * @param  array $data
     *         e.g. []
     *
     * @return array
     */
    public function bulk($data = [])
    {
        try {
            $params['body'] = collect($data)->map(function($item) {
                $item['index'] = [
                    '_index' => $this->index,
                    '_type'  => $this->type
                ];
                return $item;
            });
            $result = self::$client->bulk($params);

            return $result;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 查找记录
     *
     * @param $id
     *
     * @return array
     */
    public function find($id)
    {
        try {
            $result = self::$client->get([
                'index' => $this->index,
                'type'  => $this->type,
                'id'    => $id
            ]);

            return $result;
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 执行查询
     *
     * @param  boolean $paging 是否分页
     * @return $this
     */
    public function search($paging = false)
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => $this->where
        ];
        // 是否开启分页
        if($paging) {
            $params['from'] = $this->offset;
            $params['size'] = $this->limit;
        }
        // 是否设置排序
        if($this->order) {
            $params['body']['sort'] = $this->order;
        }
        $this->output = self::$client->search($params);

        return $this;
    }

    /**
     * 查询命中总数量
     *
     * @return int
     */
    public function count()
    {
        // 无结果时，自行查询
        if (!isset($this->output['hits']['total'])) {
            $this->search();
        }
        return isset($this->output['hits']['total']) ? $this->output['hits']['total'] : 0;
    }

    /**
     * 选择展示字段，默认所有
     *
     * @param array $columns
     *
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    /**
     * 构造 query string 查询条件
     *
     * @param  string $string 查询条件如 'package_name:"com.qq"'
     *
     * @return $this
     */
    public function queryString($string)
    {
        $where['query'] = [
            'query_string' => [
                'query' => $string
            ]
        ];
        $this->where = array_merge_recursive($this->where, $where);

        return $this;
    }

    /**
     * 构造 match query 查询条件
     *
     * @param $field
     * @param $value
     * @param string $type 类型 (_all, match, multi_match)
     *
     * @return $this
     */
    public function match($field, $value = '', $type = '_all')
    {
        switch ($type) {
            case 'match':
                $where['query'] = [
                    'match' => [
                        $field => $value
                    ]
                ];
                break;

            case 'multi_match':
                $where['query'] = [
                    'multi_match' => [
                        'type'   => 'most_fields',
                        'fields' => $field,
                        'query'  => $value
                    ]
                ];
                break;

            default:
                $where['query'] = [
                    'match' => [
                        '_all' => $field
                    ]
                ];
                break;
        }
        $this->where = array_merge_recursive($this->where, $where);

        return $this;
    }

    /**
     * 构造 term query 查询条件
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function term($field, $value)
    {
        $where['query'] = [
            'term' => [
                $field => $value
            ]
        ];
        $this->where = array_merge_recursive($this->where, $where);

        return $this;
    }

    /**
     * 构造 bool query 查询条件
     *
     * @param $field
     * @param $value
     * @param string $type 类型 (must, must_not, should, filter)
     *
     * @return $this
     */
    public function bool($field, $value, $type = 'must')
    {
        $where['query'] = [
            'bool' => [
                $type => [
                    'term' => [$field => $value]
                ]
            ]
        ];
        $this->where = array_merge_recursive($this->where, $where);

        return $this;
    }

    /**
     * 构造 range query 查询条件
     * @param string $field
     * @param array  $range          范围
     * @param array  $parameter      范围对应的 query 符号参数
     * @param array  $extraParameter 额外参数, e.g. ["format" =>"dd/MM/yyyy||yyyy"] 或 ["time_zone" => "+01:00"]
     *
     * @return $this
     */
    public function range($field, $range = [0, 100], $parameter = ['gte', 'lte'], $extraParameter = [])
    {
        $where['query'] = [
            'range' => [
                $field => [
                    [$parameter[0] => $range[0]],
                    [$parameter[1] => $range[1]]
                ]
            ]
        ];
        $extraParameter && array_push($where['query']['range'][$field], $extraParameter);
        $this->where = array_merge_recursive($this->where, $where);

        return $this;
    }

    /**
     * 包含 ID 查询
     *
     * @param  array $ids
     *
     * @return $this
     */
    public function ids(array $ids)
    {
        $where['query'] = [
            'ids' => [
                'values' => $ids
            ]
        ];
        $this->where = array_merge_recursive($this->where, $where);

        return $this;
    }

    /**
     * 设置分页参数
     *
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function limit($offset = 0, $limit = 10)
    {
        $this->limit  = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * 设置排序参数
     *
     * @param  array $order e.g ['name' => 'asc', 'age' => 'desc']
     *
     * @return $this
     */
    public function order(array $order)
    {
        $this->order = collect($order)->map(function ($item){
            return ['order' => $item];
        })->toArray();

        return $this;
    }

    /**
     * 过滤字段
     *
     * @param array $body
     *
     * @return array
     */
    public function filter(array $body)
    {
        if (!$body) {
            return [];
        }

        return collect($body)->only($this->config['index']['fields'])->all();
    }

    /**
     * 格式化输出结果
     *
     * @return array
     */
    public function outputFormat()
    {
        if (!$this->output['hits']['total']) {
            return [];
        }
        // 格式化处理
        $result = collect($this->output['hits']['hits'])->map(function ($item){
            $item['_source']['_id'] = $item['_id'];

            return in_array('*', $this->columns, true) ? $item['_source'] : collect($item['_source'])->only($this->columns)->toArray();
        });
        // 总记录数
        $result['total'] = $this->output['hits']['total'];

        return $result;
    }
}