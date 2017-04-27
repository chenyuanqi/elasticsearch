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
        $templateName = 'default_template';
        $result       = $this->index->deleteTemplate($templateName);
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试建立映射
     *
     * @group config-special
     */
    public function testCreateMapping()
    {
        $result = $this->index->createMapping();
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试清空索引 [单独执行测试]
     *
     * @group config-special
     */
    public function testClearIndex()
    {
        $result = $this->index->truncate();
        self::assertTrue($result['acknowledged']);
    }

    /**
     * 测试获取分片数量
     *
     * @group config-normal
     */
    public function testGetShardsNumber()
    {
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
        $modelName = '\App\Model\Default';
        self::assertInstanceOf($modelName, $this->index->getModel());
    }

}
