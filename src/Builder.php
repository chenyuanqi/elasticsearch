<?php

namespace chenyuanqi\elasticSearchService;

use Config;

class Builder
{
    /**
     * elastic 链接集合
     *
     * @var array
     */
    protected static $indexes = [];

    public function __construct()
    {
    }

    /**
     * 建立索引链接
     *
     * @param null $index
     *
     * @return mixed
     */
    public static function index($index = null)
    {
        if (!$index) {
            $index = Config::get('elasticsearch.default_index', 'default');
        }

        if (!isset(static::$indexes[$index])) {
            static::$indexes[$index] = new Query($index);
        }

        return static::$indexes[$index];
    }

    /**
     * 静态方法请求 Query
     *
     * @param       $method
     * @param array $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, array $parameters)
    {
        return call_user_func_array([self::index(), $method], $parameters);
    }
}
