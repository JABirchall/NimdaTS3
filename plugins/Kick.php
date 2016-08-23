<?php

namespace Plugin;

use App\Plugin;
use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Ts3Exception;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 26/07/2016
 * Time: 03:09
 */
class Kick extends Plugin implements PluginContract
{
    private $server;

    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
        $this->server = $this->teamSpeak3Bot->node;

        foreach ($this->server->clientList() as $client) {
            if (strcasecmp($client['client_nickname']->toString(), $this->info['text']) != 0) {
                continue;
            }

            try {
                $client->kick(TeamSpeak3::KICK_SERVER, "Kicked by {$this->info['invokername']}");
                $this->sendOutput("[color=green] User %s was kicked from the server.", $client['client_nickname']);
                break;
            } catch (Ts3Exception $e) {
                $message = $e->getMessage();

                if ($message === "invalid clientID") {
                    $this->sendOutput("[COLOR=red][b] There are no users online by that name");

                    return;
                }
            }
        }
    }
}
