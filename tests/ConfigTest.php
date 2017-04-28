<?php

use PHPUnit\Framework\TestCase;
use chenyuanqi\elasticsearch\Builder;

final class ConfigTest extends TestCase
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
     * 测试建立模板
     *
     * @group config-normal
     */
    public function testCreateTemplate()
    {
        echo __METHOD__."\n";
        $templateName = 'default_template';
        $result       = $this->index->createTemplate($templateName);
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试删除模板
     *
     * @group config-normal
     */
    public function testDeleteTemplate()
    {
        echo __METHOD__."\n";
        $templateName = 'default_template';
        $result       = $this->index->deleteTemplate($templateName);
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试建立映射
     *
     * @group config-normal
     */
    public function testCreateMapping()
    {
        echo __METHOD__."\n";
        $indexName = 'default';
        $typeName  = 'default';
        $result = $this->index->index($indexName)->type($typeName)->createMapping();
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试清空索引 [单独执行测试]
     *
     * @group config-special
     */
    public function testClearIndex()
    {
        echo __METHOD__."\n";
        $result = $this->index->truncate();
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试建立别名
     *
     * @group config-normal
     */
    public function testCreateAlias()
    {
        echo __METHOD__."\n";
        $aliasName = 'df_v1';
        $result    = $this->index->createAlias($aliasName);
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试别名是否存在
     *
     * @group config-normal
     */
    public function testIsAlias()
    {
        echo __METHOD__."\n";
        $aliasName = 'df_v1';
        $result    = $this->index->isAlias($aliasName);
        self::assertTrue($result);
    }

    /**
     * 测试迁移数据
     *
     *
     * @group   config-normal
     */
    public function testMigrateIndex()
    {
        //@depends testIsAlias
        echo __METHOD__."\n";
        $aliasName = 'df_v1';
        $newIndex  = 'default_migrate';
        $result = $this->index->migrateIndex($aliasName, $newIndex);
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试删除别名
     *
     * @group config-special
     */
    public function testDeleteAlias()
    {
        echo __METHOD__."\n";
        $aliasName = 'df_v1';
        $indexName = 'default_migrate';
        $result    = $this->index->index($indexName)->deleteAlias($aliasName);
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试获取分片数量
     *
     * @group config-normal
     */
    public function testGetShardsNumber()
    {
        echo __METHOD__."\n";
        $shardsNumber = 3;
        self::assertEquals($shardsNumber, $this->index->getShardsNumber());
    }

    /**
     * 测试获取批量处理限制
     *
     * @group config-normal
     */
    public function testGetLimitByConfig()
    {
        echo __METHOD__."\n";
        $limit = 10000;
        self::assertEquals($limit, $this->index->getLimitByConfig());
    }

    /**
     * 测试获取模型对象
     *
     * @group config-special
     */
    public function testGetModel()
    {
        echo __METHOD__."\n";
        $modelName = '\App\Model\Default';
        self::assertInstanceOf($modelName, $this->index->getModel());
    }

}
