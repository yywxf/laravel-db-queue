# laravel-db-queue
Dashboard for Laravel queues.

#### 环境
supervisor

#### 安装
>composer require yywxf/laravel-db-queue:dev-master

* Laravel < 5.5(>5.5的跳过此步骤)
add providers in config/app.php
> Yywxf\Queue\QueueServiceProvider::class,

* 发布资源包(包括 fontawesome,bootstrap4,layer,jq3.3.1)
>php artisan vendor:publish

>php artisan vendor:publish --tag=fontawesome

>php artisan vendor:publish --tag=bootstrap4

>php artisan vendor:publish --tag=layer

>php artisan vendor:publish --tag=js

>php artisan vendor:publish --tag=img

>如果需要覆盖资源 增加 --force 参数