<?php
error_reporting(E_ALL & ~E_NOTICE);

use App\TeamSpeak3Bot;

include_once(__DIR__ . "/vendor/autoload.php");

TeamSpeak3Bot::setOptions(\Config\TeamSpeak::$TS3config);

TeamSpeak3Bot::getInstance()->run();
