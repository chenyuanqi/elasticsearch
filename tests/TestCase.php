<?php

use chenyuanqi\elasticsearch\Builder;

abstract class TestCase extends PHPUnit\Framework\TestCase
{
    protected $index;

    public function setUp()
    {
        $this->index = new Builder(false);
    }
}