<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use chenyuanqi\elasticsearch\Builder;

final class TestQuery extends TestCase
{
    protected $index;

    public function setUp()
    {
        $this->index = new Builder(false);
    }

    public function testTraditionalToSimpleConvert()
    {
    }

}
