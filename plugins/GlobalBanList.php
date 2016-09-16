<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 16/09/2016
 * Time: 06:18
 */

namespace Plugin;


use App\Plugin;

class GlobalBanList extends Plugin implements AdvancedPluginContract
{

    public function isTriggered()
    {
        // TODO: Implement isTriggered() method.
    }

    public function install()
    {
        Manager::schema()->create($this->CONFIG['table'], function(Blueprint $table) {
            $table->increments('id');
            $table->text('uid');
            $table->text('added_by');

            $table->timestamps();
        });
    }

    public function update($version)
    {
        // TODO: Implement update() method.
    }
}