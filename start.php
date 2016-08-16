<?php

use App\TeamSpeak3Bot;
use Config\TeamSpeak;

include_once(__DIR__ . "/vendor/autoload.php");

TeamSpeak3Bot::setOptions((new TeamSpeak)->config);

TeamSpeak3Bot::getInstance()->run();

