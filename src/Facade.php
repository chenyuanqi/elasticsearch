<?php

namespace chenyuanqi\elasticSearchService;

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