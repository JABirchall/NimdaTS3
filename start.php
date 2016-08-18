<?php

use App\TeamSpeak3Bot;

include_once(__DIR__ . "/vendor/autoload.php");

TeamSpeak3Bot::setOptions((new \Config\TeamSpeak)->config);

TeamSpeak3Bot::getInstance()->run();
