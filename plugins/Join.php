<?php

namespace Plugins;

use App\Plugin;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 29/07/2016
 * Time: 11:12
 */
class Join extends Plugin
{
    private $server;

    function isTriggered()
    {

        $this->server = $this->teamSpeak3Bot->node;

        $client = $this->server->clientGetById($this->info['clid']);
        $channel = $this->server->channelGetById($this->info['ctid']);

        $this->teamSpeak3Bot->sendServerMsg("{$client} has switched to channel {$channel}.");
        $this->teamSpeak3Bot->printOutput("{$client} has switched to channel {$channel}.");
    }

}