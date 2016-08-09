<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 09/08/2016
 * Time: 04:03
 */

namespace Plugin;


use App\Plugin;

class BadClientName extends Plugin implements PluginContract
{

    private $server;

    public function isTriggered()
    {
        $this->server = $this->teamSpeak3Bot->node;

        // TODO: Implement isTriggered() method.
    }

}