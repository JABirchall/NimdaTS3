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
    function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }

        $output = "md5({$this->info['text']}) => " . md5($this->info['text']);
        $this->sendOutput($output);
    }
}

