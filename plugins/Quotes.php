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

        if($this->info['text']->startsWith('find')) {
            $text = $this->info['text']->substr(strlen('find') + 1);
            $quote = Quote::where('quote', 'LIKE', '%{$text}%')->first();
            $this->sendOutput($quote->quote);
        } elseif($this->info['text']->startsWith('add')) {
            $text = $this->info['text']->substr(strlen('add') + 1);
            $text = $text->split(' ', 2);
            $quote = Quote::create([
                'username' => $text[0],
                'quote' => $text[1],
            ]);
            $this->sendOutput($quote->quote . " Created successfully");
        } elseif($this->info['text']->startsWith('delete')) {
            $text = $this->info['text']->substr(strlen('delete') + 1);
            $quote = Quote::where('quote', 'LIKE', '%{$text}%')->delete();
            $this->sendOutput($quote->id . " Removed successfully");
        }
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