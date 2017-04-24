<?php

use PHPUnit\Framework\TestCase;
use chenyuanqi\elasticsearch\Builder;

final class QueryTest extends TestCase
{
    /**
     * 索引实例
     *
     * @var
     */
    protected $index;

    /**
     * 准备测试
     */
    public function setUp()
    {
        $this->index = new Builder(false);
    }
}
