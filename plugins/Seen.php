<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28/07/2016
 * Time: 15:30
 */
class Seen extends Plugin
{
    private $server;
    function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
        $this->server = $this->teamSpeak3Bot->node;

    }
}