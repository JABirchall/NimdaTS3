<?php

use App\Plugin;
use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Ts3Exception;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 26/07/2016
 * Time: 03:09
 */
class Kick extends Plugin
{
    private $server;

    function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
        $this->server = $this->teamSpeak3Bot->node;

        foreach ($this->server->clientList() as $client) {
            //echo $client['client_nickname']->toString() . " <> " . $this->info['text'] ." = ". strcasecmp($client['client_nickname']->toString(), $this->info['text']);

            if (strcasecmp($client['client_nickname']->toString(), $this->info['text']) === 0) {
                try {
                    $client->kick(TeamSpeak3::KICK_SERVER, "Kicked by {$this->info['invokername']}");
                    $output = "[color=green] User {$client['client_nickname']->toString()} was kicked from the server.";
                    $this->sendOutput($output);
                } catch (Ts3Exception $e) {
                    $message = $e->getMessage();
                    $admin = $this->server->clientGetByName($this->info['invokername']);
                    if ($message === "invalid clientID") {
                        $admin->poke("[COLOR=red][b] There are no users online by that name");
                        $output = "[COLOR=red][b] There are no users online by that name";
                        $this->sendOutput($output);

                        return;
                    }
                    echo $message;
                }

                return;
            }
        }
    }
}