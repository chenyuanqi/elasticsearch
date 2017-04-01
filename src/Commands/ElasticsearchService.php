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
     * name 为索引配置名称，action 为操作名称 [new update bulk clear]
     *
     * @var string
     */
    protected $signature = 'elastic:search {name} {action}';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start        = microtime(true);
        $this->name   = $this->argument('name');
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

                case 'clear':
                    $this->clear();
                    break;

                default:
                    $this->error('操作 '.$this->action.' 未发现，目前仅支持 new, update, bulk, clear 操作');
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
        $index = (new Builder)->index($this->name);
        $index->createIndex();
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
        $index = (new Builder)->index($this->name);
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
        $index = (new Builder)->index($this->name);
        $model = $index->getModel();
        $limit = $index->getLimitByConfig();

        $model->chunk($limit, function ($datas) use ($index){
            collect($datas)->map(function ($data) use ($index){
                $item                 = $index->filter($data);
                $item['index']['_id'] = sprintf('%s%s', $data->id, $data->apps_type);

                return $item;
            });

            $index->bulk($params);
        });

        $this->info('批量写入数据结束');
    }

    /**
     * 清除索引操作
     *
     * @return void
     */
    protected function clear()
    {
        $this->info(sprintf('清除 %s 索引开始', $this->name));
        $index = (new Builder)->index($this->name);
        $index->truncate();
        $this->info(sprintf('清除 %s 索引结束', $this->name));
    }
}
