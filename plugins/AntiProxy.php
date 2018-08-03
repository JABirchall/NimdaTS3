<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 11/06/2018
 * Time: 13:06
 */

namespace Plugin;

use App\Plugin;
use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Ts3Exception;


class AntiProxy extends Plugin implements PluginContract
{
    private $api = "http://check.getipintel.net/check.php?ip=%s&contact=%s&flags=m&format=json";

    public function isTriggered()
    {
        if (empty($this->CONFIG['email'])) {
            $this->sendOutput("[%s] No email entry in configuration, API requires an email for registration.", __CLASS__);
            return;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, sprintf($this->api, $this->info['client_address']->toString(), $this->CONFIG['email']));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

        $response = json_decode(curl_exec($curl));

        if(curl_errno($curl) != 0) {
            curl_close($curl);
            return;
        }

        curl_close($curl);

        if($response->status !== "success") {
            $this->sendOutput("[%s] %s", __CLASS__, $response->message);
            return;
        }

        switch ($response->result) {
            case 1:
                $this->kickClient();
                break;

            case $response->result >= $this->CONFIG['kick_threshold']:
                $this->kickClient();
                break;

            case $response->result < 0:
                $this->sendOutput("[%s] %s", __CLASS__, $response->message);
                return;
            default:
                return;
        }
    }

    private function kickClient()
    {
        try {
            $client = $this->teamSpeak3Bot->node->clientGetByUid($this->info['client_unique_identifier']);
            $client->kick( TeamSpeak3::KICK_SERVER, "Detected using a Proxy, Socks4/5 or VPN.");
        } catch(Ts3Exception $e) {
            return;
        }
    }

}
