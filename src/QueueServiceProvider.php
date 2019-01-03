<?php

namespace Yywxf\Queue;

use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (! defined('DBQUEUE_PATH')) {
            define('DBQUEUE_PATH', realpath(__DIR__.'/../'));
        }

        // 视图
        $this->loadViewsFrom(DBQUEUE_PATH . '/resources/views', 'queue');
        // 路由
        $this->loadRoutesFrom(DBQUEUE_PATH . '/src/routes.php');

        $this->publishes([
            DBQUEUE_PATH . '/src/config/dbqueue.php' => config_path('dbqueue.php'), // 配置文件
            // __DIR__ . '/views' => resource_path('views'),
        ]);

        $this->publishes([
            DBQUEUE_PATH . '/resources/assets/fontawesome' => public_path('fontawesome'),
        ], 'fontawesome');

        $this->publishes([
            DBQUEUE_PATH . '/resources/assets/bootstrap' => public_path('bootstrap'),
        ], 'bootstrap');

        $this->publishes([
            DBQUEUE_PATH . '/resources/assets/img' => public_path('img'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }
}
