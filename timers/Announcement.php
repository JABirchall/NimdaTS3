<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 14/02/2017
 * Time: 15:24
 */

namespace Timer;


use App\Timer;


class Announcement extends Timer implements TimerContract
{
    public function isTriggered()
    {
        $this->teamSpeak3Bot->node->message($this->CONFIG['message']);
    }
}