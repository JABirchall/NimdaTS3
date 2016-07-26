<?php

use App\Plugin;
use TeamSpeak3\Ts3Exception;
class ClientInfo extends Plugin
{

    private $server;
    /**
     * @return sendOutput
     */
    function isTriggered()
    {
        if(!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);
            return;
        }
        $this->server = $this->teamSpeak3Bot->node;
        try{
            $Client = $this->server->clientGetByName($this->info['invokername']);
            $name = $this->info['text'];
            $clientInfo = $this->server->clientInfoDb($this->server->clientFindDb($this->info['text']));

            $Client->message("[COLOR=blue][B]{$name}: Database ID: {$clientInfo["client_database_id"]}");
            $Client->message("[COLOR=blue][B]{$name}: Unique ID: {$clientInfo["client_unique_identifier"]}");
            $Client->message("[COLOR=blue][B]{$name}: Joined: ".date("F j, Y, g:i a",$clientInfo["client_created"]));
            $Client->message("[COLOR=blue][B]{$name}: Last connection: ". date("F j, Y, g:i a",$clientInfo["client_lastconnected"]));
            $Client->message("[COLOR=blue][B]{$name}: Total connections: {$clientInfo["client_totalconnections"]}");
            $clientInfo["client_description"] = ($clientInfo["client_description"])?$clientInfo["client_description"]:"N/A";
            $Client->message("[COLOR=blue][B]{$name}: Client description: {$clientInfo["client_description"]}");
            $Client->message("[COLOR=blue][B]{$name}: Last IP: {$clientInfo["client_lastip"]}");
        }catch (Ts3Exception $e)
        {
            $message = $e->getMessage();
            $admin = $this->server->clientGetByName($this->info['invokername']);
            $admin->message("[color=red][b]ERROR : {$message}");
        }

    }
}