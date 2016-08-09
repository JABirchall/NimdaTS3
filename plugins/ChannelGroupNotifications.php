<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 09/08/2016
 * Time: 04:21
 */

namespace Plugin;


use App\Plugin;

class ChannelGroupNotifications extends Plugin implements PluginContract
{

    private $server;

    public function isTriggered()
    {
        if($this->info['ctid'] != $this->CONFIG['channelId']) {
            return;
        }

        $this->server = $this->teamSpeak3Bot->node;

        $notifyClient = $this->server->clientGetById($this->info['clid']);
        $channel = $this->server->channelGetById($this->info['ctid']);


        foreach($this->server->clientList() as $client) {
            foreach ($client->memberOf() as $group) {

                if ($group != $this->CONFIG['groupId']) {
                    continue;
                }

                $client->message("[url=client:///{$notifyClient->getUniqueId()}]{$client->toString()}[/url] has join channel [url=channelid://{$channel->getId()}]{$channel->toString()}[/url]");
                $channel->message("Please wait for a staff member to assist you, I have notified them you are here.");
            }
        }
    }

}