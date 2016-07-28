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

        try {
            $client = $this->server->clientGetByName($this->info['invokername']);
            $name = $this->info['text'];
            $seen = $this->server->clientInfoDb($this->server->clientFindDb($this->info['text']));

            $client->message("[COLOR=blue][B]User {$name} was last seen on " .
                             date("F j, Y, g:i a", $seen["client_lastconnected"]));

        } catch (Ts3Exception $e) {
            $message = $e->getMessage();
            $admin = $this->server->clientGetByName($this->info['invokername']);
            $admin->message("[color=red][b]ERROR : {$message}");
        }

    }
}