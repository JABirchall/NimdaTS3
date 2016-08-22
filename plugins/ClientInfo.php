<?php

namespace Plugin;

use App\Plugin;
use Carbon\Carbon;
use TeamSpeak3\Ts3Exception;

class ClientInfo extends Plugin implements PluginContract
{

    private $server;

    /**
     * @return sendOutput
     */
    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
        $this->server = $this->teamSpeak3Bot->node;

        try {
            $name = $this->info['text'];
            $clientInfo = $this->server->clientInfoDb($this->server->clientFindDb($this->info['text']));

            $this->sendOutput("[COLOR=blue][B]%s - Database ID: %s", $name, $clientInfo["client_database_id"]);
            $this->sendOutput("[COLOR=blue][B]%s - Unique ID: %s", $name, $clientInfo["client_unique_identifier"]);
            $this->sendOutput("[COLOR=blue][B]%s - First joined: %s", $name, Carbon::createFromTimestamp($clientInfo["client_created"])->toDateTimeString());
            $this->sendOutput("[COLOR=blue][B]%s - Last connection: %s", $name, Carbon::createFromTimestamp($clientInfo["client_lastconnected"])->diffForHumans());
            $this->sendOutput("[COLOR=blue][B]%s - Total connections: %s", $name, $clientInfo["client_totalconnections"]);
            $this->sendOutput("[COLOR=blue][B]%s - Client description: %s", $name, ($clientInfo["client_description"]) ? $clientInfo["client_description"] : "N/A");
            $this->sendOutput("[COLOR=blue][B]%s - Last IP: %s", $name, $clientInfo["client_lastip"]);
        } catch (Ts3Exception $e) {
            $this->sendOutput("[color=red][b]ERROR : %s", $e->getMessage());
        }

    }
}