<?php

namespace Plugins;

use App\Plugin;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 26/07/2016
 * Time: 02:37
 */
class Jail extends Plugin
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
            $suspects = $this->server->clientFind($this->info['text']);
            $jail = $this->server->channelGetById($this->CONFIG['channel']);

            foreach ($suspects as $suspect) {
                $suspect = $this->server->clientGetById($suspect["clid"]);
                $suspect->move($jail);
                $suspect->poke("[COLOR=red][b] You have been put in jail by {$this->info['invokername']}");
                $output = "User {$suspect['client_nickname']} was put in jail by {$this->info['invokername']}";
                $this->sendOutput($output);
            }
        } catch (Ts3Exception $e) {
            $message = $e->getMessage();
            $admin = $this->server->clientGetByName($this->info['invokername']);
            if ($message === "invalid clientID") {
                $admin->poke("[COLOR=red][b] There are no users online by that name");

                return;
            }
            echo $message;
        }
    }

}