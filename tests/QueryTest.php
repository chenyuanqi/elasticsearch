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
     * 准备基境
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
     * @group        query-create
     */
    public function testInsertData($id, $title, $content)
    {
        echo __METHOD__."\n";
        $data = [
            'title'   => $title,
            'content' => $content
        ];

        $result = $this->index->insert($data, $id);
        self::assertTrue($result['created']);
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
     * @group        query-update
     */
    public function testUpdateById($id, $title, $content)
    {
        echo __METHOD__."\n";
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
     * @group        query-update
     */
    public function testInsertOrUpdate($id, $title, $content)
    {
        echo __METHOD__."\n";
        $data = [
            'title'   => $title,
            'content' => $content
        ];

        $result = $this->index->insertOrUpdate($data, $id);
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

    /**
     * 测试根据 ID 查找数据
     *
     * @param string $id
     *
     * @dataProvider additionIdProvider
     * @group        query-search
     */
    public function testFindById($id)
    {
        echo __METHOD__."\n";
        $result = $this->index->find($id);
        self::assertTrue($result['found']);
    }

    /**
     * 测试多重检索数据
     *
     * @group query-search
     */
    public function testMergeGet()
    {
        echo __METHOD__."\n";
        $data   = [
            ['index' => 'default_migrate', 'type' => 'default', 'id' => ['1', '2'], 'include' => 'title'],
            ['index' => '.kibana', 'type' => 'config',  'id' => '4.5.1', 'include' => ['title'], 'exclude' => ['fields']],
            [
                'index' => 'laravel-error-2017-04-25',
                'type'  => 'laravel-error-2017-04-25',
                'id'    => 'AVui3C5Dp6HZ_LBoZqoX',
            ],
        ];

        $result = $this->index->mget($data);
        self::assertTrue($result['docs'][0]['found']);
        self::assertTrue($result['docs'][1]['found']);
        self::assertTrue($result['docs'][2]['found']);
    }

    /**
     * 测试批量增删改
     *
     * @group query-crud
     */
    public function testBulk()
    {
        echo __METHOD__."\n";
        $params = [
            [
                'index',
                '_id'     => 1,
                'title'   => 'viki',
                'content' => 'test11111',
            ],
            [
                'update',
                '_id'   => 1,
                'title' => 'vikey',
            ],
            [
                'delete',
                '_id' => 2,
            ],
            [
                'index',
                '_id'     => 2,
                'title'   => 'append',
                'content' => 'append will be expected!',
            ],
        ];
        $result = $this->index->bulk($params);
        self::assertFalse($result['errors']);
    }

    /**
     * 测试根据 ids 检索数据
     *
     * @group query-search
     */
    public function testIds()
    {
        echo __METHOD__."\n";
        $ids    = [1, 2, 3];
        $result = $this->index->index('default')
                              ->type('default')
                              ->ids($ids)
                              ->search();
        self::assertEquals(3, $result['total']);
    }

    /**
     * 测试 queryString 查询
     *
     * @group query-search
     */
    public function testQueryString()
    {
        echo __METHOD__."\n";
        $query  = 'title:"航空"';
        $result = $this->index->index('default')
                              ->type('default')
                              ->queryString($query)
                              ->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试根据 ID 删除数据
     *
     * @param string $id
     *
     * @dataProvider additionIdProvider
     * @group        query-delete
     */
    public function testDeleteById($id)
    {
        echo __METHOD__."\n";
        $result = $this->index->deleteById($id);
        self::assertEquals(0, $result['_shards']['failed']);
    }

    /**
     * 拆除基境
     */
    public function tearDown()
    {
        $this->index = null;
    }

}
