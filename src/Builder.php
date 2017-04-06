<?php

namespace chenyuanqi\elasticsearch;

use Config;

class Builder
{
    /**
     * query 单例
     *
     * @var string
     */
    protected static $query;

    public function __construct()
    {
    }

    /**
     * 获取 query 实例
     *
     * @return mixed
     */
    public static function query()
    {
        if (!isset(static::$query)) {
            static::$query = new Query();
        }

        return static::$query;

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
        return call_user_func_array([self::query(), $method], $arguments);
    }
}
