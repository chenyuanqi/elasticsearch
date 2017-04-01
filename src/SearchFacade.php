<?php

namespace chenyuanqi\elasticsearch;

class SearchFacade extends \Illuminate\Support\Facades\Facade
{
    /**
     * 注册 Search Facade
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'search';
    }
}