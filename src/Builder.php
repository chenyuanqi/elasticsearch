<?php

namespace chenyuanqi\elasticSearchService;

use Elasticsearch\ClientBuilder;
use Config;

class Builder
{
    protected $indexes = [];

    public function __construct()
    {
    }

    public function index($index = null)
    {
        if (!$index) {
            $index = Config::get('elasticsearch.default_index', 'default');
        }

        if (!isset($this->indexes[$index])) {
            $this->indexes[$index] = new Query($index);
        }

        return $this->indexes[$index];
    }

    public function __call($method, array $parameters)
    {
        return call_user_func_array([$this->index(), $method], $parameters);
    }
}
