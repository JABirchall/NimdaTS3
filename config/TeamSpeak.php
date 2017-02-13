<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 10/08/2016
 * Time: 22:44
 */
namespace Config;

class TeamSpeak
{

    public static $TS3config = [
        'username' => 'serveradmin',
        'password' => 'password',
        'host' => '127.0.0.1',
        'port' => 10011,
        'name' => 'Nimda',
        'serverPort' => 9987,
        'timeout' => 1,
        'misc' => [
            'debug' => false,
            'newLineNewMessage' => false,
            'ignoreWarnings' => false
        ]
    ];

}
