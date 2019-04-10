Chat
===
Chat

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist jakharbek/yii2-chat "*"
```

or add

```
"jakharbek/yii2-chat": "*"
```

to the require section of your `composer.json` file.

### Directory Structure

```
api/                 api
assets/              assets
command/             daimons and console
dto/                 data transfer object
exceptions/          exceptions
factory/             factories
forms/               forms
interfaces/          interfaces
migrations/          migrations
models/              models
repositories/        repositories
services/            services
```

Usage
-----

В данном расширение нет разделение разрешение и прав доступа, данную особеность осталось на ваши усмотрение для больее гибкости расширение.


Для начало использование вам следует применить миграции:

```php
yii migrate --migrationPath=@vendor/jakharbek/yii2-chat/migrations
```
 
после вам нужно применить Bootstrap класс.

```php
\jakharbek\chat\Bootstrap
```

API
-----

Для его использование вам нужно подключить контроллер.


```php
...
controllerMap => [
'server' => \jakharbek\chat\api\ChatController::class
]
...
```

или же можете скопировать его или взять от него наследование и подключить это тоже на ваше усмотрение

Подключение сокета
-----
Запуск веб-сокета сервера
-----
Вам нужно подключить в ваше консольное приложение контроеллер веб-сокета: 
```php
...
controllerMap => [
'server' => \jakharbek\chat\commands\ServerController::class
]
...
```

После запустить это консольную комманду как daimon.
На пример
```php
php yii server/start [port]
```

Подключение asset'а
-----
```php
\jakharbek\chat\assets\ChatAsset::register($this);
```

Пример подключение сокет клиента.
-----
```php

$js = <<<JS
var chat = new ChatClient("ws://localhost:8080",'{$token}','{chat_id}');
chat.onMessage = function(type,data,event){
    //сдесь будет ваша логика для UI
}
chat.init();
$(".msg_send_btn").click(function () {
    chat.sendMessage($('.write_msg').val());
});
JS;

$this->registerJs($js);
```

