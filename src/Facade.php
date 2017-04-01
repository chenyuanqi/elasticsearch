<?php

namespace chenyuanqi\elasticsearch;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * 注册 Search Facade
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Search';
    }
}