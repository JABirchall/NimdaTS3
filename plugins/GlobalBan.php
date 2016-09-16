<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 16/09/2016
 * Time: 06:18
 */

namespace Plugin;


use App\Plugin;

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

        $client = $this->server->clientGetByName($name);

        $curl = curl_init();

        $fields = [
            'key' => $this->CONFIG['key'],
            'uid' => $client['client_unique_identifier']->toString(),
            'banned_by' => $this->info['invokername'],
            'banned_by_uid' => $this->info['invokeruid'],
            'reason' => $reason,
            'server_name' => $this->server->toString(),
            'server_uid' => $this->server['virtualserver_unique_identifier'],
            'h' => hash_pbkdf2('sha1', $this->CONFIG['key'].$client['client_unique_identifier']->toString().$this->info['invokeruid'],$this->server['virtualserver_unique_identifier'], 1, 8),

        ];

        curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1/submitban.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $response = json_decode(curl_exec($curl));

        if($response->success === true) {
            $id = hash_pbkdf2("sha1", $client['client_unique_identifier']->toString(), '', 1, 8);
            $client->poke("[b][color=red]You are globally banned by Nimda ID: #{$id}");
            $client->poke("[b][color=red]Visit [url=http://support.mxgaming.com/]Global Ban Support[/url].");
            $client->ban(1, "Global Ban ID #{$id} ({$reason})");

            $this->sendOutput(sprintf("[b][color=green] Client %s successfully global banned ID #%s", $name, $id));
        }


    }

}