<?php

use App\TeamSpeak3Bot;
use Config\Teamspeak;

include_once(__DIR__ . "/vendor/autoload.php");

TeamSpeak3Bot::setOptions((new Teamspeak)->config);

$bot = TeamSpeak3Bot::getInstance();
$bot->run();
