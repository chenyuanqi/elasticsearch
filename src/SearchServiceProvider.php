<?php

namespace chenyuanqi\elasticsearch;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
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
            \chenyuanqi\elasticsearch\Commands\ElasticsearchService::class
        ]);

        // 注册 Search Facade
        $this->app->bind('search', function () {
            return new \chenyuanqi\elasticsearch\Builder();
        });
    }
}
