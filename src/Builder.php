<?php

namespace chenyuanqi\elasticsearch;

class Builder
{
    /**
     * 是否使用 laravel 框架 (默认使用)
     * @var bool
     */
    protected static $is_laravel = true;

    /**
     * query 单例
     *
     * @var string
     */
    protected static $query;

    public function __construct($is_laravel = true)
    {
        static::$is_laravel = $is_laravel;
    }

    /**
     * 获取 query 实例
     *
     * @return mixed
     */
    public static function query()
    {
        if (null === static::$query) {
            static::$query = new Query(static::$is_laravel);
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
