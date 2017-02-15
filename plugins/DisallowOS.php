<?php

namespace Plugin;

use App\Plugin;
use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Ts3Exception;

class DisallowOS extends Plugin implements PluginContract
{

    public function isTriggered()
    {
        try {
            $client = $this->teamSpeak3Bot->node->clientGetById($this->info['clid']);
        } catch(Ts3Exception $e) {
            return;
        }

        if(in_array($client['client_platform'], $this->CONFIG['disallowed'])) {
            $client->kick(TeamSpeak3::KICK_SERVER, $this->CONFIG['msg']);
        }
    }
}
