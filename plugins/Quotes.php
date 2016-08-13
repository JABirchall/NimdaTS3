<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 10/08/2016
 * Time: 01:38
 */

namespace Plugin;


use App\Plugin;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Plugin\Models\Quote;

class Quotes extends Plugin implements AdvancedPluginContract
{

    public function isTriggered()
    {
        if (!isset($this->info['text'])) {
            $this->sendOutput($this->CONFIG['usage']);

            return false;
        }

        if ($this->info['text']->startsWith('find')) {
            $text = $this->info['text']->substr(strlen('find') + 1);

            if (!$text || empty($text)) {
                $this->sendOutput($this->CONFIG['usage']);

                return false;
            }

            $ids = Quote::select('id')
                ->where('quote', 'LIKE', "%{$text}%")
                ->pluck('id')
                ->toArray();

            $ids = implode(',', $ids);
            $this->sendOutput("Found matching quotes: {$ids}.");

            return true;
        } elseif ($this->info['text']->startsWith('add')) {
            $text = $this->info['text']->substr(strlen('add') + 1);
            $text = $text->split(' ', 2);

            if (!$text[0] || !$text[1] || empty($text[1])) {
                $this->sendOutput($this->CONFIG['usage']);

                return false;
            }

            $quote = Quote::create([
                'username' => $text[0],
                'quote' => $text[1],
            ]);

            $this->sendOutput("[{$quote->username}]: {$quote->quote} [b]- [color=green]Created successfully");

            return true;
        } elseif ($this->info['text']->startsWith('delete')) {

            $id = $this->info['text']->substr(strlen('delete') + 1);
            $quote = Quote::find($id->toInt())
                    ->delete();

            $this->sendOutput("{$id->toInt()} [b]- [color=green]Removed successfully");

            return true;

        } elseif ($this->info['text']->isInt()) {
            $quote = Quote::where('id', $this->info['text']->toInt())->first();

            $time = Carbon::parse($quote->created_at)->diffForHumans();

            $this->sendOutput("[{$quote->username}]: {$quote->quote} [b]- Created {$time}");

            return true;
        } else {
            $this->sendOutput($this->CONFIG['usage']);

            return false;
        }
    }

    public function install()
    {
        Manager::schema()->create($this->CONFIG['table'], function(Blueprint $table) {
            $table->increments('id');
            $table->text('username');
            $table->text('quote');

            $table->timestamps();
        });

        echo "Install ";
    }

    public function update($version)
    {
        switch ($version) {
            case version_compare($version, $this->CONFIG['version'], '<'):
                // update logic
                return true;
            default:
                return true;
        }
    }

}