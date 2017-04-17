<?php

namespace chenyuanqi\elasticsearch\Commands;

use Illuminate\Console\Command;
use chenyuanqi\elasticsearch\Builder;

/**
 * Elasticsearch Service 命令行
 *
 * @package App\Console\Commands
 */
class ElasticsearchService extends Command
{
    /**
     * 命令名称及参数
     * name 为索引配置名称，action 为操作名称 [new update bulk copy clear]
     *
     * @var string
     */
    protected $signature = 'elastic:search {name} {type} {action} {copy_name?} {?copy_type?}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = 'elasticsearch 批量索引操作';

    /**
     * 索引配置名称
     *
     * @var string
     */
    protected $name;

    /**
     * 索引类型名称
     *
     * @var string
     */
    protected $type;

    /**
     * 操作名称
     *
     * @var
     */
    protected $action;
    /**
     * 创建命令行实例
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 执行处理
     *
     * @return mixed
     */
    public function handle()
    {
        $start        = microtime(true);
        $this->name   = $this->argument('name');
        $this->type   = $this->argument('type');
        $this->action = $this->argument('action');

        if (!\Config::get('elasticsearch.'.$this->name, [])) {
            $this->error('配置文件内未发现 elasticsearch.'.$this->name);
        } else {
            switch ($this->action) {
                case 'new':
                    $this->mapping();
                    break;

                case 'update':
                    $this->updateMapping();
                    break;

                case 'bulk':
                    $this->bulk();
                    break;

                case 'copy':
                    $name = $this->argument('copy_name');
                    $type = $this->argument('copy_type');
                    $this->copy($name, $type);
                    break;

                case 'clear':
                    $this->clear();
                    break;

                default:
                    $this->error('操作 '.$this->action.' 未发现，目前仅支持 new, update, bulk, copy, clear 操作');
                    break;
            }
        }

        $end = microtime(true);
        $this->info('命令大约执行了 '.ceil($end - $start).' 秒');
    }

    /**
     * 新建索引操作
     *
     * @return void
     */
    protected function mapping()
    {
        $this->info('新建索引及映射开始');
        $index = (new Builder)->index($this->name)->type($this->type);
        $index->createMapping();
        $this->info('新建索引及映射结束');
    }

    /**
     * 更新索引操作
     *
     * @return void
     */
    protected function updateMapping()
    {
        $this->info('更新映射开始');
        $index = (new Builder)->index($this->name)->type($this->type);
        $index->updateMapping();
        $this->info('更新映射结束');
    }

    /**
     * 批量写入数据
     *
     * @return void
     */
    protected function bulk()
    {
        $this->info('批量写入数据开始');
        $index = (new Builder)->index($this->name)->type($this->type);
        $model = $index->getModel();
        $limit = $index->getLimitByConfig();

        $model->chunk($limit, function ($datas) use ($index){
            $params = collect($datas)->map(function ($data) use ($index){
                $item        = $data->toArray();
                $item['_id'] = sprintf('%s', $data->id);

                return $item;
            });

            $index->bulk($params);
        });

        $this->info('批量写入数据结束');
    }

    /**
     * 复制索引操作
     *
     * @param  string $name 要复制的索引名称
     * @param  string $type 要复制的索引类型
     *
     * @return void
     */
    protected function copy($name, $type)
    {
        if(!$name || !$type) {
            $this->error('批量复制的索引名称及类型参数不能为空！');
            exit();
        }

        $this->info('批量复制数据开始');
        $targetIndex = (new Builder)->index($name)->type($type);
        $targetData  = $targetIndex->scroll(1000, '30s', 'scan')->search();

        while (true) {
            $scrollId = $targetData['_scroll_id'];

            // 移除多余参数，便于 bulk 批量写入数据
            array_pull($targetData, '_scroll_id');
            array_pull($targetData, 'total');

            // 批量复制
            $needle = collect($targetData)->map(function ($item){
                $item[0] = 'index';

                return $item;
            })->toArray();
            (new Builder)->index($this->name)->type($this->type)->bulk($needle);

            $targetData = $targetIndex->searchByScrollId($scrollId)->outputFormat();
            if (!$targetData) {
                break;
            }
        }

        $this->info('批量复制数据结束');
    }

    /**
     * 清除索引操作
     *
     * @return void
     */
    protected function clear()
    {
        $this->info(sprintf('清除 %s 索引开始', $this->name));
        $index = (new Builder)->index($this->name)->type($this->type);
        $index->truncate();
        $this->info(sprintf('清除 %s 索引结束', $this->name));
    }
}
