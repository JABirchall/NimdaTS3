<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 09/08/2016
 * Time: 03:50
 */

namespace Plugin;


use App\Plugin;

class BadChannelName extends Plugin implements PluginContract
{

    private $server;

    public function isTriggered()
    {
        $this->server = $this->teamSpeak3Bot->node;

        // TODO: Implement isTriggered() method.


    }
}