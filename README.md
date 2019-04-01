# laravel-db-queue
Dashboard for Laravel queues.

#### 环境
supervisor

#### 安装
>composer require yywxf/laravel-db-queue:dev-master

* Laravel < 5.5(>5.5的跳过此步骤)
add providers in config/app.php
> Yywxf\Queue\QueueServiceProvider::class,

* 发布资源包(包括 fontawesome 和 bootstrap4)
>php artisan vendor:publish

>php artisan vendor:publish --tag=fontawesome

>php artisan vendor:publish --tag=bootstrap4