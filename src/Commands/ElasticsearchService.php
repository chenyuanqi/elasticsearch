<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use chenyuanqi\elasticSearchService\Index;

class ElasticsearchService extends Command
{
    /**
     * The name and signature of the console command.
     * $indexType 索引配置名
     * $action 操作名 [new bulk clear]
     *
     * @var string
     */
    protected $signature = 'elastic:search {name} {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'elasticsearch 批量索引操作';

    /**
     * Create a new command instance.
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
        $start = microtime(true);
        $this->name   = $this->argument('name');
        $this->action = $this->argument('action');

        if (!\Config::get('elasticsearch.'.$this->name, [])) {
            $this->error('配置文件内未发现 elasticsearch.'.$this->name);
        } else {
            switch ($this->action) {
                case 'new':
                    $this->mapping();
                    break;

                case 'clear':
                    $this->clear();
                    break;

                default:
                    $this->error('操作 '.$this->action.' 未发现，目前仅支持 new, clear 操作');
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
        $this->info('新建索引开始');
        $index = new Index($this->name);
        $index->mapping();
        $this->info('新建索引结束');
    }

    /**
     * 清除索引操作
     *
     * @return void
     */
    protected function clear()
    {
        $this->info(sprintf('清除 %s 索引开始', $this->name));
        $index = new Index($this->name);
        $index->clear();
        $this->info(sprintf('清除 %s 索引结束', $this->name));
    }
}
