<?php
namespace jakharbek\chat\commands;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use yii\console\Controller;

/**
 * Class ServerController
 * @package jakharbek\chat\commands
 */
class ServerController extends Controller
{
    /**
     * @param int $port
     */
    public function actionStart($port = 8080)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            $port
        );

        $server->run();
    }
}