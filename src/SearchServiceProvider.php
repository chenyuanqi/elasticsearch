<?php

namespace chenyuanqi\elasticsearch;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * 是否延迟加载
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * 启动应用服务 (发布 elasticsearch 配置)
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/elasticsearch.php' => config_path('elasticsearch.php'),
        ], 'config');
    }

    /**
     * 注册应用服务 (注册 elasticsearch Commands 和 Search Facade)
     *
     * @return void
     */
    public function register()
    {
        // 注册命令文件
        $this->commands([
           Commands\ElasticsearchService::class
        ]);

        // 注册 Search Facade
        $this->app->bind('Search', function ($app) {
            return new \chenyuanqi\elasticsearch\Builder();
        });
    }
}
