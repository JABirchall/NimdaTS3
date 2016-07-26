<?php
use App\TeamSpeak3Bot;

include_once("vendor/autoload.php");

TeamSpeak3Bot::setOptions([
    'username' => 'serveradmin',
    'password' => '15t9u54i',
    'host' => '127.0.0.1',
    'port' => '10011',
    'name' => 'DrBot',
    'serverPort' => '9987',
]);

$bot = TeamSpeak3Bot::getInstance();
$bot->run();
