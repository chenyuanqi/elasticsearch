<?php

namespace chenyuanqi\elasticSearchService;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
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
     * Register the application services.
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
            return new \chenyuanqi\elasticSearchService\Builder();
        });
    }
}
