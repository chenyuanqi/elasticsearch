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
     *
     * @param string $id
     * @param string $title
     * @param string $content
     *
     * @dataProvider additionProvider
     * @group        query-crud
     */
    public function testInsertData($id, $title, $content)
    {
        $data = [
            'title'   => $title,
            'content' => $content
        ];

        $result = $this->index->insert($data, $id);
        self::assertEquals(0, $result['_shards']['failed']);
    }

    /**
     * 测试根据 ID 更新数据
     *
     * @param string $id
     * @param string $title
     * @param string $content
     *
     * @dataProvider additionProvider
     * @group        query-crud
     */
    public function testUpdateById($id, $title, $content)
    {
        $data = [
            'title'   => $title,
            'content' => $content
        ];

        $result = $this->index->updateById($data, $id);
        self::assertEquals(0, $result['_shards']['failed']);
    }

    /**
     * 测试根据 ID 更新数据
     *
     * @param string $id
     * @param string $title
     * @param string $content
     *
     * @dataProvider additionProvider
     * @group        query-crud
     */
    public function testInsertOrUpdate($id, $title, $content)
    {
        $data = [
            'title'   => $title,
            'content' => $content
        ];

        $result = $this->index->insertOrUpdate($data, $id);
        self::assertEquals(0, $result['_shards']['failed']);
    }

    /**
     * 测试根据 ID 删除数据
     *
     * @param string $id
     *
     * @dataProvider additionIdProvider
     * @group        query-crud
     */
    public function testDeleteById($id)
    {
        $result = $this->index->deleteById($id);
        self::assertEquals(0, $result['_shards']['failed']);
    }

    /**
     * _source 数据提供器
     *
     * @return array
     */
    public function additionProvider()
    {
        return [
            'One'   => ['1', '國際航空電視臺1', 'content1'.str_random(6)],
            'Two'   => ['2', '國際航空電視臺2', 'content2'.str_random(6)],
            'Three' => ['3', '國際航空電視臺3', 'content3'.str_random(6)],
            'Four'  => ['4', '國際航空電視臺4', 'content4'.str_random(6)],
            'Five'  => ['5', '國際航空電視臺5', 'content5'],
        ];
    }

    /**
     * id 数据提供器
     *
     * @return array
     */
    public function additionIdProvider()
    {
        return [
            'One'   => ['1'],
            'Two'   => ['2'],
            'Three' => ['3'],
            'Four'  => ['4'],
            'Five'  => ['5'],
        ];
    }
}
