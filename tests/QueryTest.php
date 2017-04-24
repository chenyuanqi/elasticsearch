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

    /**
     * 测试写入数据
     */
    public function testInsert()
    {
        $data = [
            'title'   => str_random(6).'國際航空電視臺',
            'content' => str_random(30)
        ];
        $result = $this->index->insert($data);
        self::assertTrue($result['created']);
    }
}
