<?php

namespace Plugin;

use App\Plugin;
use Carbon\Carbon;
use TeamSpeak3\Ts3Exception;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28/07/2016
 * Time: 15:30
 */
class Seen extends Plugin implements PluginContract
{
    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }

        try {
            $name = $this->info['text'];
            $seen = $this->teamSpeak3Bot->node->clientInfoDb($this->teamSpeak3Bot->node->clientFindDb($this->info['text']));

            $this->sendOutput("[COLOR=blue][B]User %s was last seen on %s", $name, Carbon::createFromTimestamp($seen["client_lastconnected"])->diffForHumans());
        } catch (Ts3Exception $e) {
            $this->sendOutput("[color=red][b]ERROR : %s", $e->getMessage());
        }
    }
}
