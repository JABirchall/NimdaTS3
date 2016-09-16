<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 16/09/2016
 * Time: 06:18
 */

namespace Plugin;

use App\Plugin;
use TeamSpeak3\Ts3Exception;

class GlobalBan extends Plugin implements PluginContract
{

    public $server;

    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
        $this->server = $this->teamSpeak3Bot->node;

        list($name, $reason) = $this->info['text']->split(' ');

        try {
            $client =  current($this->server->clientFind($name));
            $client = $this->server->clientGetById($client['clid']);
        }catch(Ts3Exception $e){
            $message = $e->getMessage();
            if ($message === "invalid clientID") {
                $this->sendOutput("[COLOR=red][b] There are no users online by that name");

                return;
            }
        }
        $curl = curl_init();

        $fields = [
            'key' => $this->CONFIG['key'],
            'uid' => $client['client_unique_identifier']->toString(),
            'banned_by' => $this->info['invokername'],
            'banned_by_uid' => $this->info['invokeruid'],
            'reason' => $reason,
            'server_name' => $this->server->toString(),
            'server_uid' => $this->server['virtualserver_unique_identifier'],
            'h' => hash_pbkdf2('sha1', sprintf("%s-%s-%s", $this->CONFIG['key'], $client['client_unique_identifier']->toString(),$this->info['invokeruid']),$this->server['virtualserver_unique_identifier'], 1, 8),

        ];

        curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1/submitban.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $response = json_decode(curl_exec($curl));

        if($response->success === true) {
            try {
                $id = hash_pbkdf2("sha1", $client['client_unique_identifier']->toString(), '', 1, 8);
                $client->poke("[b][color=red]You are global banned by Nimda ID: #{$id}");
                $client->poke("[b][color=red]Visit [url=http://support.mxgaming.com/]Global Ban Support[/url].");
                $client->ban(1, "Global Ban ID #{$id} ({$reason})");
            }catch(Ts3Exception $e){
                return;
            }

            $this->sendOutput(sprintf("[b][color=green] Client %s successfully global banned ID #%s", $client, $id));
        } elseif ($response->success === false && $response->err === 0x02) {
            $this->sendOutput("[COLOR=red][b]This server is not authorized to global ban, email support@mxgaming.com");
        }
    }

}