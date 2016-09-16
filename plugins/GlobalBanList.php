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


class GlobalBanList extends Plugin implements PluginContract
{

    private $server;

    public function isTriggered()
    {
        $this->server = $this->teamSpeak3Bot->node;

        $whitelisted = Whitelist::where('uid', $this->info['client_unique_identifier']->toString())->count();
        if($whitelisted >= 1) {
            return;
        }
        
        $curl = curl_init();

        $fields = [
            'uid' => $this->info['client_unique_identifier']->toString()
        ];

        curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1/globalban.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        if($response->success === true && $response->banned === true && $response->uid === $this->info['client_unique_identifier']->toString()) {

            $client = $this->server->clientGetByUid($this->info['client_unique_identifier']);
            $id = hash_pbkdf2("sha1", $this->info['client_unique_identifier']->toString(), '', 1, 8);
            $client->poke("[b][color=red]You are globally banned by Nimda ID: #{$id}");
            $client->poke("[b][color=red]Visit [url=http://support.mxgaming.com/]Global Ban Support[/url].");
            $client->ban(1, "Nimda Global Ban ID: #{$id}");
        }
    }

}
