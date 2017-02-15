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
    private $notifyClient;
    private $channel;

    public function isTriggered()
    {
        if(!in_array($this->info['ctid'], $this->CONFIG['channelIds'])) {
            return;
        }

        $this->notifyClient = $this->teamSpeak3Bot->node->clientGetById($this->info['clid']);

        if($this->notifyClient['client_type'] === 1) {
            return;
        }

        $this->channel = $this->teamSpeak3Bot->node->channelGetById($this->info['ctid']);
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
        foreach ($this->teamSpeak3Bot->node->clientList() as $client) {
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
                    $client->message("[b][color=green]{$clientUrl} has joined the channel {$channelUrl}");
                } catch (Ts3Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        try {
            $this->channel->message("Please wait for a staff member to assist you, I have notified them you are here.");
        } catch (Ts3Exception $e) {
            echo $e->getMessage() . $e->getTraceAsString();
        }
    }
}