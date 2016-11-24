<?php

namespace Plugin;

use App\Plugin;
use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Ts3Exception;

class DisallowOS extends Plugin implements PluginContract
{
    private $server;

    public function isTriggered()
    {
        $this->disallowed = $this->CONFIG['disallowed'];

        $this->server = $this->teamSpeak3Bot->node;
        try {
            $client = $this->server->clientGetById($this->info['clid']);
            $clientInfo = $this->server->clientInfoDb($this->server->clientFindDb($client['client_nickname']));
        } catch(Ts3Exception $e) {
            return;
        }

        if(in_array($this->server->clientGetById($this->info['clid'])['client_platform'],$this->disallowed)) {       
            $client->kick(TeamSpeak3::KICK_SERVER,"You are using disallowed OS!");
        }
    }
}
