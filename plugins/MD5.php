<?php

namespace Plugin;

use App\Plugin;

/**
 * Class MD5
 */
class MD5 extends Plugin implements PluginContract
{

    /**
     * @return sendOutput
     */
    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }

        $this->sendOutput("md5(%s) => %s", $this->info['text'], md5($this->info['text']));
    }
}

