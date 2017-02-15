<?php

namespace Plugin;

use App\Plugin;
use TeamSpeak3\Ts3Exception;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 26/07/2016
 * Time: 02:37
 */
class Jail extends Plugin implements PluginContract
{
    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }

        try {
            $suspects = $this->teamSpeak3Bot->node->clientFind($this->info['text']);
            $jail = $this->teamSpeak3Bot->node->channelGetById($this->CONFIG['channel']);

            foreach ($suspects as $suspect) {
                $suspect = $this->teamSpeak3Bot->node->clientGetById($suspect["clid"]);
                $suspect->move($jail);
                $suspect->poke("[COLOR=red][b] You have been put in jail by {$this->info['invokername']}");
                $this->sendOutput("User %s was put in jail by %s", $suspect['client_nickname'], $this->info['invokername']);
            }
        } catch (Ts3Exception $e) {
            $message = $e->getMessage();
            if ($message === "invalid clientID") {
                $this->sendOutput("[COLOR=red][b] There are no users online by that name");

                return;
            }
            echo $message;
        }
    }
}
