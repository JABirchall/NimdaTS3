<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 16/09/2016
 * Time: 08:22
 */

namespace Plugin;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Plugin\Models\Whitelist;
use App\Plugin;

class AddWhitelist extends Plugin implements AdvancedPluginContract
{
    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }

        Whitelist::create([
            'uid' => $this->info['text'],
            'added_by' => $this->info['invokername'],
            'added_by_uid' => $this->info['invokeruid']
        ]);

        $this->sendOutput('Unique ID: %s has been successfully whitelisted.', $this->info['text']);
    }

    public function install()
    {
        Manager::schema()->create($this->CONFIG['table'], function(Blueprint $table) {
            $table->increments('id');
            $table->text('uid');
            $table->text('added_by');
            $table->text('added_by_uid');

            $table->timestamps();
        });
    }

    public function update($version)
    {
        // TODO: Implement update() method.
    }
}