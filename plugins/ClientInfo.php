<?php

namespace Plugin;

use App\Plugin;
use TeamSpeak3\Ts3Exception;

class ClientInfo extends Plugin implements PluginInterface
{

    private $server;

    /**
     * @return sendOutput
     */
    function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
        $this->server = $this->teamSpeak3Bot->node;

        try {
            $client = $this->server->clientGetByName($this->info['invokername']);
            $name = $this->info['text'];
            $clientInfo = $this->server->clientInfoDb($this->server->clientFindDb($this->info['text']));

            $client->message("[COLOR=blue][B]{$name}: Database ID: {$clientInfo["client_database_id"]}");
            $client->message("[COLOR=blue][B]{$name}: Unique ID: {$clientInfo["client_unique_identifier"]}");
            $client->message("[COLOR=blue][B]{$name}: Joined: " . date("F j, Y, g:i a", $clientInfo["client_created"]));
            $client->message("[COLOR=blue][B]{$name}: Last connection: " .
                             date("F j, Y, g:i a", $clientInfo["client_lastconnected"]));
            $client->message("[COLOR=blue][B]{$name}: Total connections: {$clientInfo["client_totalconnections"]}");

            $clientInfo["client_description"] = ($clientInfo["client_description"]) ? $clientInfo["client_description"] : "N/A";
            $client->message("[COLOR=blue][B]{$name}: Client description: {$clientInfo["client_description"]}");
            $client->message("[COLOR=blue][B]{$name}: Last IP: {$clientInfo["client_lastip"]}");
        } catch (Ts3Exception $e) {
            $message = $e->getMessage();
            $admin = $this->server->clientGetByName($this->info['invokername']);
            $admin->message("[color=red][b]ERROR : {$message}");
        }

    }
}