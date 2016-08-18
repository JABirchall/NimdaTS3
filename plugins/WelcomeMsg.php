<?php

namespace Plugin;

use App\Plugin;

class WelcomeMsg extends Plugin implements PluginContract
{
    private $server;

    public function isTriggered()
    {
        $this->server = $this->teamSpeak3Bot->node;

        $client = $this->server->clientGetById($this->info['clid']);
        $clientDbId = current($this->server->clientFindDb($client['client_nickname']));

        $clientInfo = $this->server->clientInfoDb($clientDbId);

        $format = [
            "%CL_DATABASE_ID%"      => $clientInfo["client_database_id"],
            "%CL_UNIQUE_ID%"        => $clientInfo["client_unique_identifier"],
            "%CL_COUNTRY%"          => $client['client_country'],
            "%CL_NAME%"             => $client['client_nickname'],
            "%CL_VERSION%"          => $client['client_version'],
            "%CL_PLATFORM%"         => $client['client_platform'],
            "%CL_IP%"               => $client['connection_client_ip'],
            "%CL_CREATED%"          => $clientInfo["client_created"],
            "%CL_TOTALCONNECTIONS%"  => $clientInfo["client_totalconnections"],
        ];

        $msg = strtr($this->CONFIG['msg'], $format);

        $client->message($msg);
    }

}