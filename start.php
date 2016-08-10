<?php

use App\TeamSpeak3Bot;

include_once(__DIR__ . "/vendor/autoload.php");

TeamSpeak3Bot::setOptions((new \Config\Teamspeak)->config);

$bot = TeamSpeak3Bot::getInstance();
$bot->run();
