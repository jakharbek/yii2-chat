<?php

namespace jakharbek\chat\assets;

use Yii;
use yii\web\AssetBundle;
/**
 * Class ChatAsset
 * @package jakharbek\chat\assets
 */
class ChatAsset extends AssetBundle
{
    public $sourcePath = '@vendor/jakharbek/yii2-chat/assets';

    public $js = [
        'js/ChatClient.js'
    ];


    public static function path($file = ""){
        return Yii::$app->assetManager->getBundle(self::className())->baseUrl."/".$file;
    }


    public $depends = [
        \yii\web\JqueryAsset::class
    ];
}