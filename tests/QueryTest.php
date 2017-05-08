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
     * @param int    $score
     *
     * @dataProvider additionProvider
     * @group        query-create
     */
    public function testInsertData($id, $title, $content, $score)
    {
        echo __METHOD__."\n";
        $data = [
            'title'   => $title,
            'content' => $content,
            'score'   => $score
        ];

        $result = $this->index->insert($data, $id);
        self::assertTrue($result['created']);
    }

    /**
     * 测试写入或覆盖数据
     *
     * @param string $id
     * @param string $title
     * @param string $content
     * @param int    $score
     *
     * @dataProvider additionProvider
     * @group        query-create
     */
    public function testInsertOrCoverData($id, $title, $content, $score)
    {
        echo __METHOD__."\n";
        $data = [
            'title'   => $title,
            'content' => $content,
            'score'   => $score
        ];

        $result = $this->index->insertOrCover($data, $id);
        self::assertEquals(0, $result['_shards']['failed']);
    }

    /**
     * 测试根据 ID 更新数据
     *
     * @param string $id
     * @param string $title
     * @param string $content
     * @param int    $score
     *
     * @dataProvider additionProvider
     * @group        query-update
     */
    public function testUpdateById($id, $title, $content, $score)
    {
        echo __METHOD__."\n";
        $data = [
            'title'   => $title,
            'content' => $content,
            'score'   => $score
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
     * @param int    $score
     *
     * @dataProvider additionProvider
     * @group        query-update
     */
    public function testInsertOrUpdate($id, $title, $content, $score)
    {
        echo __METHOD__."\n";
        $data = [
            'title'   => $title,
            'content' => $content,
            'score'   => $score
        ];

        $result = $this->index->insertOrUpdate($data, $id);
        self::assertEquals(0, $result['_shards']['failed']);
    }

    /**
     * 测试根据条件更新
     *
     * @group query-update
     */
    public function testUpdateByConditions()
    {
        echo __METHOD__."\n";
        $query  = 'title:"國際航空電視臺5"';
        $result = $this->index->queryString($query)->update(['content' => 'test update by conditions', 'score' => 1]);
        self::assertEmpty($result['failures']);
    }

    /**
     * 测试自增
     *
     * @group query-update
     */
    public function testIncrease()
    {
        echo __METHOD__."\n";
        $query  = 'title:"國際航空電視臺5"';
        $result = $this->index->queryString($query)->increase('score', 2);
        self::assertEmpty($result['failures']);
    }

    /**
     * 测试自减
     *
     * @group query-update
     */
    public function testDecrease()
    {
        echo __METHOD__."\n";
        $query  = 'title:"國際航空電視臺5"';
        $result = $this->index->queryString($query)->decrease('score');
        self::assertEmpty($result['failures']);
    }

    /**
     * _source 数据提供器
     *
     * @return array
     */
    public function additionProvider()
    {
        return [
            'One'   => ['1', '國際航空電視臺1', 'content1'.str_random(6), random_int(1, 10)],
            'Two'   => ['2', '國際航空電視臺2', 'content2'.str_random(6), random_int(1, 10)],
            'Three' => ['3', '國際航空電視臺3', 'content3'.str_random(6), random_int(1, 10)],
            'Four'  => ['4', '國際航空電視臺4', 'content4'.str_random(6), random_int(1, 10)],
            'Five'  => ['5', '國際航空電視臺5', 'content5', random_int(1, 10)],
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
     * 测试查询最小值
     *
     * @group query-search
     */
    public function testMinScore()
    {
        $result = $this->index->min('score');
        self::assertGreaterThan(0, $result);
    }

    /**
     * 测试查询最大值
     *
     * @group query-search
     */
    public function testMaxScore()
    {
        $result = $this->index->max('score');
        self::assertGreaterThan(0, $result);
    }

    /**
     * 测试查询字段总和
     *
     * @group query-search
     */
    public function testSumScore()
    {
        $result = $this->index->sum('score');
        self::assertGreaterThan(0, $result);
    }

    /**
     * 测试查询字段平均值
     *
     * @group query-search
     */
    public function testAvgScore()
    {
        $result = $this->index->avg('score');
        self::assertGreaterThan(0, $result);
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
                '_id'     => 6,
                'title'   => 'viki',
                'content' => 'test11111',
                'score'   => 9
            ],
            [
                'update',
                '_id'   => 6,
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
                'score'   => 1
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
     * 测试滚屏搜索
     *
     * @group query-search
     */
    public function testScroll()
    {
        echo __METHOD__."\n";
        $result = $this->index->scroll(60)->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试滚屏ID搜索
     *
     * @group query-special
     */
    public function testScrollById()
    {
        echo __METHOD__."\n";
        $searchResult = $this->index->scroll(60)->search();
        $result = $this->index->searchByScrollId($searchResult['_scroll_id']);
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试获取搜索结果第一条数据
     *
     * @group query-search
     */
    public function testGetFirstData()
    {
        echo __METHOD__."\n";
        $result = $this->index->first();
        self::assertNotEmpty($result['_id']);
    }

    /**
     * 测试获取搜索结果总数量
     *
     * @group query-search
     */
    public function testGetTotalNumber()
    {
        echo __METHOD__."\n";
        $result = $this->index->count();
        self::assertGreaterThan(0, $result);
    }

    /**
     * 测试搜索显示字段
     *
     * @group query-search
     */
    public function testSelectFields()
    {
        echo __METHOD__."\n";
        $result = $this->index->pluck('title', 'score')->first();
        self::assertNotEmpty($result['title']);
        self::assertNotEmpty($result['score']);
        self::assertFalse(isset($result['content']));
    }

    /**
     * 测试 query 是否有效
     *
     * @group query-search
     */
    public function testValidateQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->match('title', '航空', 'match')->term('title', 'vikey')->validateQuery();
        self::assertFalse($result);
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
     * 测试匹配查询
     *
     * @group query-search
     */
    public function testMatchQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->match('title', '航空', 'match')->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试 term 搜索
     *
     * @group query-search
     */
    public function testTermQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->term('title', 'vikey')->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试前缀搜索
     *
     * @group query-search
     */
    public function testPrefixQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->prefix('content', 'con')->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试 bool 搜索
     *
     * @group query-search
     */
    public function testBoolQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->bool('content', 'test11111')->search();
        self::assertEquals(1, $result['total']);
    }

    /**
     * 测试 range 搜索
     *
     * @group query-search
     */
    public function testRangeQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->range('score', [1, 9])->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试 where in 搜索
     *
     * @group query-search
     */
    public function testWhereInQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->whereIn('score', [1, 2, 3])->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试 where not in 搜索
     *
     * @group query-search
     */
    public function testWhereNotInQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->whereNotIn('score', [1, 2, 3])->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试 where 搜索
     *
     * @group query-search
     */
    public function testWhereQuery()
    {
        echo __METHOD__."\n";
        $result1 = $this->index->where('title', 'vikey')->search();
        self::assertEquals(1, $result1['total']);

        $result2 = $this->index->where('score', '>', 3)->search();
        self::assertGreaterThan(0, $result2['total']);

        $result3 = $this->index->where('score', '<', 5)->search();
        self::assertGreaterThan(0, $result3['total']);

        $result4 = $this->index->where('content', 'like', 'content%')->search();
        self::assertGreaterThan(0, $result4['total']);
    }

    /**
     * 测试 or where 搜索
     *
     * @group query-search
     */
    public function testOrWhereQuery()
    {
        echo __METHOD__."\n";
        $result1 = $this->index->where('title', 'vikey')->orWhere('content', 'like', 'test%')->search();
        self::assertEquals(1, $result1['total']);
    }

    /**
     * 测试 where between 搜索
     *
     * @group query-search
     */
    public function testWhereBetweenQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->whereBetween('score', [1, 5])->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试 where between 搜索
     *
     * @group query-search
     */
    public function testWhereNotBetweenQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->whereNotBetween('score', [1, 5])->search();
        self::assertGreaterThan(0, $result['total']);
    }

    /**
     * 测试 limit 搜索
     *
     * @group query-search
     */
    public function testLimitQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->limit(0, 1)->search(true);
        self::assertTrue(isset($result[0]));
        self::assertFalse(isset($result[1]));
    }

    /**
     * 测试 order 搜索
     *
     * @group query-search
     */
    public function testOrderByQuery()
    {
        echo __METHOD__."\n";
        $result = $this->index->orderBy(['score' => 'desc'])->search();
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
     * 测试根据条件删除数据
     *
     * @group query-special
     */
    public function testDeleteByConditions()
    {
        echo __METHOD__."\n";
        $result = $this->index->term('title', 'vikey')->delete();
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
