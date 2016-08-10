<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 10/08/2016
 * Time: 01:38
 */

namespace Plugin;


use App\Plugin;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Plugin\Models\Quote;

class Quotes extends Plugin implements AdvancedPluginContract
{

    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return;
        }
        Quote::create([
            'username' =>'tagKnife',
            'quote' => 'I like to code',
        ]);

        dd(Quote::first()->toArray());
    }

    public function install()
    {

        Manager::schema()->create($this->CONFIG['table'], function (Blueprint $table){
            $table->increments('id');
            $table->text('username');
            $table->text('quote');

            $table->timestamps();
        });

        echo "Install ";

    }

}