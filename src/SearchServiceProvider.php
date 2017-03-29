<?php

namespace chenyuanqi\elasticSearchService;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
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

        $this->publishes([
            __DIR__.'/Commands/ElasticsearchService.php' => app_path('Console/Commands/ElasticsearchService.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
