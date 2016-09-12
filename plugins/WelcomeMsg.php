<?php

namespace Plugin;

use App\Plugin;
use Carbon\Carbon;

class WelcomeMsg extends Plugin implements PluginContract
{
    private $server;

    public function isTriggered()
    {
        $this->server = $this->teamSpeak3Bot->node;

        $client = $this->server->clientGetById($this->info['clid']);
        $clientInfo = $this->server->clientInfoDb($this->server->clientFindDb($client['client_nickname']));

        $format = [
            "%CL_DATABASE_ID%"      => $clientInfo["client_database_id"],
            "%CL_UNIQUE_ID%"        => $clientInfo["client_unique_identifier"],
            "%CL_COUNTRY%"          => $client['client_country'],
            "%CL_NAME%"             => $client['client_nickname'],
            "%CL_VERSION%"          => $client['client_version'],
            "%CL_PLATFORM%"         => $client['client_platform'],
            "%CL_IP%"               => $client['connection_client_ip'],
            "%CL_CREATED%"          => Carbon::createFromTimestamp($clientInfo["client_created"])->toDayDateTimeString(),
            "%CL_TOTALCONNECTIONS%"  => $clientInfo["client_totalconnections"],
        ];

        $msg = strtr($this->CONFIG['msg'], $format);

        $this->sendOutput($msg);
    }
}
