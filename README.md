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


Usage
-----

В данном расширение нет разделение разрешение и прав доступа, данную особеность осталось на ваши усмотрение для больее гибкости расширение.


Для начало использование вам следует применить миграции:

```php
yii migrate --migrationPath=@vendor/jakharbek/yii2-chat/migrations
```

Чат имеет раздение на типы (diolog_type) это как комната но не совсем скорей вариация, на пример:
```php
Объевление
Личное
и тд.
```
Вам нужно определить какие типы у вас будут их может быть несколько так и только один.

Для примера сообъшение отправлиное в диалоговом типе объевление между пользователем 1 и 2 не пересекаются в другом типе.

Методы и классы (API)
-----

ChatRepository
-----
Получение всех чатов пользователя
```php
getChats($user_id, $status = ChatsUsers::STATUS_ACTIVE)
```
Пример:
```php
$repository = Yii::createObject(ChatRepository::class);
$chats = $repository->getChats(1);
```
Получение последнего сообщение
```php
getLastSentMessage($chat_id)
```
Пример:
```php
$repository = Yii::createObject(ChatRepository::class);
$messages = $repository->getLastSentMessage($chat_id);
```
Получение всех сообщение чат
```php
getMessages($chat_id, $status = Messages::STATUS_SENT)
```
Получение всех сообщение чата виде запроса (ActiveQuery), удобно в случии использование провайдера данных
```php
getMessagesQuery($chat_id, $status = Messages::STATUS_SENT)
```
Получение чата (явлается системный функций в своей основе, скорей всего вам не пригодится)
```php
getChat($diolog_1, $diolog_2, $diolog_type)
```

ChatServices
-----
Отправка сообщение
```php
sendMessage(sendMessageChatDTO $sendMessageChatDTO)
```
Пример:
```php
$service = Yii::createObject(['class' => ChatServices::class]);
    $sendMessageChatDTO = new sendMessageChatDTO();
    $sendMessageChatDTO->to_user_id = 1;
    $sendMessageChatDTO->from_user_id = 2;
    $sendMessageChatDTO->message = "Hello World!";
    $sendMessageChatDTO->diolog_type = "personal";
    $sendMessageChatDTO->diolog_1 = 1;
    $sendMessageChatDTO->diolog_2 = 2;
$msg = $service->sendMessage($sendMessageChatDTO);
```
Отметить сообщение как прочитаное
```php
setSeenMessagesById($messages_id, $user_id = null)
```

Пример:
```php
$service = Yii::createObject(['class' => ChatServices::class]);
$messages = $service->setSeenMessagesById($message_id, $user_id);
```

Удалить (отметить как удалёное) сообщение

```php
deleteMessage($message_id, $user_id, $delete_for, $deleteAll = false)
```

Пример:
```php
$service = Yii::createObject(['class' => ChatServices::class]);
$messages = $service->deleteMessage($message_id, $user_id,$delete_for, $deleteAll);
```

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

Подключение asset'а
-----
```php
\jakharbek\chat\assets\ChatAsset::register($this);
```

Пример подключение сокет клиента.
-----
```php

$js = <<<JS
var chat = new ChatClient("ws://localhost:8080",'{$token}','{$diolog_1}','{$diolog_2}','{$diolog_type}',"");
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

