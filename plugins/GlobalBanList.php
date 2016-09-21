<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 16/09/2016
 * Time: 06:18
 */

namespace Plugin;


use App\Plugin;
use Plugin\Models\Whitelist;
use TeamSpeak3\Ts3Exception;


class GlobalBanList extends Plugin implements PluginContract
{

    private $server;

    public function isTriggered()
    {
        if($this->CONFIG['enabled'] === false) {
            return;
        }

        $this->server = $this->teamSpeak3Bot->node;

        $whitelisted = Whitelist::where('uid', $this->info['client_unique_identifier']->toString())->count();
        if($whitelisted >= 1) {
            return;
        }

        $curl = curl_init();

        $fields = [
            'uid' => $this->info['client_unique_identifier']->toString()
        ];

        curl_setopt($curl, CURLOPT_URL, 'http://52.174.144.155/bans/check');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $response = json_decode(curl_exec($curl));

        if(curl_errno($curl) != 0) {
            curl_close($curl);
            return;
        }

        curl_close($curl);

        if($response->success === false || $response->banned === false) {
            return;
        }

        try {
            $client = $this->server->clientGetByUid($this->info['client_unique_identifier']);
        }catch(Ts3Exception $e){
            return;
        }

        if($this->CONFIG['ban'] === true && $response->uid === $this->info['client_unique_identifier']->toString()) {
            try {
                $client->poke("[b][color=red]You are globally banned by Nimda ID: #{$response->ban_id}");
                $client->poke("[b][color=red]Visit [url=#]Global Ban Support[/url].");
                $client->ban(0, "Global Ban ID #{$response->ban_id} ({$response->reason})");
            }catch(Ts3Exception $e){
                return;
            }
        }

        if($this->CONFIG['alert'] === true) {
            $message = sprintf("[ALERT] Client %s is global banned ID #%s reason: %s issued Global Ban from: %s\n", $client, $response->ban_id, $response->reason, $response->server_name);
            array_walk(array_map([$this->server, 'serverGroupGetById'], $this->CONFIG['alert_groups']), function($admin) use ($message) {
                $admin->message($message);
            });
        }

        printf("[%s]: %s", $this->teamSpeak3Bot->carbon->now()->toTimeString(), $message);
    }

}
