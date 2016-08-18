<?php

namespace Plugin;

use App\Plugin;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 29/07/2016
 * Time: 11:12
 */
class Join extends Plugin implements PluginContract
{
    private $server;

    public function isTriggered()
    {

        $this->server = $this->teamSpeak3Bot->node;

        $client = $this->server->clientGetById($this->info['clid']);
        $channel = $this->server->channelGetById($this->info['ctid']);

        $this->teamSpeak3Bot->sendServerMsg("%s has switched to channel %s.", $client, $channel);
        $this->teamSpeak3Bot->printOutput("%s has switched to channel %s.", $client, $channel);
    }

}