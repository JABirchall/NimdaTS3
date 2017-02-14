<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 14/02/2017
 * Time: 14:21
 */

namespace App;

use \Carbon\Carbon;


class Timer
{
    protected $time;
    protected $teamSpeak3Bot;

    public function __construct($config, $teamSpeak3Bot)
    {
        $this->time = Carbon::now();
        $this->teamSpeak3Bot = $teamSpeak3Bot;
        foreach ($config as $name => $value) {
            switch ($name) {
                case "name":
                    $this->name = $value;
                    break;

                case "author":
                    $this->author = $value;
                    break;

                case "description":
                    $this->description = $value;
                    break;

                case "version":
                    $this->version = $value;
                    break;

                case "interval":
                    $this->interval = $value;
                    break;

                case "lastRunTime":
                    $this->lastRunTime = $this->time->micro;
                    break;

                case "nextRunTime":
                    $this->time->addHours($this->interval->hours)
                        ->addMinutes($this->interval->minutes)
                        ->addSeconds($this->interval->seconds);
                    $this->nextRunTime = $this->time->micro;

                case "configFile":
                    $this->configFile = $value;
                    break;

                default:
                    $this->CONFIG[$name] = $value;
                    break;
            }
        }

    }


    public function handle()
    {

    }


}