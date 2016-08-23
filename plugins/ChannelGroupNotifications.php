<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 09/08/2016
 * Time: 04:21
 */

namespace Plugin;


use App\Plugin;
use TeamSpeak3\Ts3Exception;

class ChannelGroupNotifications extends Plugin implements PluginContract
{

    private $server;
    private $notifyClient;
    private $channel;


    public function isTriggered()
    {
        if ($this->info['ctid'] != $this->CONFIG['channelId']) {
            return;
        }

        $this->server = $this->teamSpeak3Bot->node;

        $this->notifyClient = $this->server->clientGetById($this->info['clid']);
        $this->channel = $this->server->channelGetById($this->info['ctid']);

        foreach ($this->notifyClient->memberOf() as $group) {
            if ($group->getId() != $this->CONFIG['guestGroupId']) {
                continue;
            } else {
                $this->notify();
            }
        }
    }

    protected function notify()
    {
        foreach ($this->server->clientList() as $client) {

            if ($client["client_type"] == 1) {
                continue;
            }

            foreach ($client->memberOf() as $group) {
                if ($group->getId() != $this->CONFIG['groupId']) {
                    continue;
                }
                $clientUrl = "[url=client://{$this->notifyClient->getId()}/{$this->notifyClient['client_unique_identifier']->toString()}]{$this->notifyClient->toString()}[/url]";
                $channelUrl = "[url=channelid://{$this->channel->getId()}/]{$this->channel->toString()}[/url]";
                try {
                    $client->message("[b]%s has joined channel %s", $clientUrl, $channelUrl);
                } catch (Ts3Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        try {
            $this->channel->message("Please wait for a staff member to assist you, I have notified them you are here.");
        } catch (Ts3Exception $e) {
            echo $e->getMessage();
        }
    }
}