<?php

namespace jakharbek\chat;


use jakharbek\chat\interfaces\iChatsRepository;
use jakharbek\chat\interfaces\iChatsServices;
use jakharbek\chat\repositories\ChatRepository;
use jakharbek\chat\services\ChatServices;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        $container = \Yii::$container;
        $container->set(iChatsRepository::class, ChatRepository::class);
        $container->set(iChatsServices::class, ChatServices::class);
    }
}