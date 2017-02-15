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
    protected $nextRunTime;
    public $teamSpeak3Bot;

    public function __construct($config, $teamSpeak3Bot)
    {
        $this->time = Carbon::now();
        $this->nextRunTime = Carbon::now();
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
                    $this->setNextRunTime();
                    break;

                case "lastRunTime":
                    $this->lastRunTime = $this->time->timestamp;
                    break;

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
        if($this->nextRunTime->timestamp <= $this->time->now()->timestamp) {
            $this->isTriggered();
            $this->setNextRunTime();
        }
    }

    public function setNextRunTime()
    {
        $this->nextRunTime->addHours($this->interval['hours'])
            ->addMinutes($this->interval['minutes'])
            ->addSeconds($this->interval['seconds']);
    }
}
