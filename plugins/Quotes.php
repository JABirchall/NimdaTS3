<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 10/08/2016
 * Time: 01:38
 */

namespace Plugin;


use App\Plugin;
use Plugin\Models\Quote;

class Quotes extends Plugin implements AdvancedPluginContract
{

    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
    }

    public function install()
    {
        echo "install";
        // TODO: Implement install() method.
    }

}