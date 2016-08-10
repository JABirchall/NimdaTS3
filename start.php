<?php

use App\TeamSpeak3Bot;

include_once(__DIR__ . "/vendor/autoload.php");

TeamSpeak3Bot::setOptions([
    'username' => 'serveradmin',
    'password' => 'Tyc00n..',
    'host' => '127.0.0.1',
    'port' => '10011',
    'name' => 'Nimda',
    'serverPort' => '9987',
]);

$bot = TeamSpeak3Bot::getInstance();
$bot->run();
