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
    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }

        list($name, $reason) = $this->info['text']->split(' ');

        try {
            $client =  current($this->teamSpeak3Bot->node->clientFind($name));
            $client = $this->teamSpeak3Bot->node->clientGetById($client['clid']);
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
            'ip' => $client['connection_client_ip']->toString(),
            'banned_by' => $this->info['invokername']->toString(),
            'banned_by_uid' => $this->info['invokeruid']->toString(),
            'reason' => $reason->toString(),
            'server_name' => $this->teamSpeak3Bot->node->toString(),
            'server_uid' => $this->teamSpeak3Bot->node['virtualserver_unique_identifier']->toString(),
            'h' => hash_pbkdf2('sha1', sprintf("%s-%s-%s", $this->CONFIG['key'], $client['client_unique_identifier']->toString(),$this->info['invokeruid']),$this->teamSpeak3Bot->node['virtualserver_unique_identifier']->toString(), 1, 8),

        ];

        curl_setopt($curl, CURLOPT_URL, 'http://52.174.144.155/bans/submit');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $response = json_decode(curl_exec($curl));
        if(curl_errno($curl) != 0) {
            $this->sendOutput(sprintf("There was a error with the request: %s", curl_error($curl)));
            return;
        }

        curl_close($curl);

        if($response->success === true) {
            try {
                $client->poke("[b][color=red]You are global banned by Nimda ID: #{$response->ban_id}");
                $client->poke("[b][color=red]Visit [url=#]Global Ban Support[/url].");
                $client->ban(0, "Global Ban ID #{$response->ban_id} ({$reason})");
            }catch(Ts3Exception $e){
                return;
            }

            $this->sendOutput(sprintf("[b][color=green] Client %s successfully global banned ID #%s", $client, $response->ban_id));
        } elseif ($response->success === false && $response->err === 0x02) {
            $this->sendOutput("[COLOR=red][b]This server is not authorized to global ban, email support@mxgaming.com");
        } else {
            $this->sendOutput("[COLOR=orange][b]Unexpected error");
        }
    }

}