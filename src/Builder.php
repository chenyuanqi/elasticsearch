<?php

namespace chenyuanqi\elasticsearch;

use Config;

class Builder
{
    /**
     * elastic 链接集合
     *
     * @var array
     */
    protected static $indexes = [];

    protected static $index = '';

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
        if(!$index && !self::$index) {
            self::$index = Config::get('elasticsearch.default_index', 'default');
        } elseif($index) {
            self::$index = $index;
        }

        if (!isset(static::$indexes[self::$index ])) {
            static::$indexes[self::$index ] = new Query(self::$index );
        }

        return static::$indexes[self::$index ];
    }

    /**
     * 静态方法请求 Query
     *
     * @param       $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        return call_user_func_array([self::index(), $method], $arguments);
    }
}
