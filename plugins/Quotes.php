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
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

            if(!$ids) {
                $this->sendOutput("No results found matching your query.");
                return;
            }

            $ids = implode(',', $ids);
            $this->sendOutput("Found matching quotes: %s", $ids);

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

            $this->sendOutput("[%s]: %s [b]- [color=green]Created successfully", $quote->username, $quote->quote);

            return true;
        } elseif ($this->info['text']->startsWith('delete')) {

            $id = $this->info['text']->substr(strlen('delete') + 1);
            try {
                $quote = Quote::findOrFail($id->toInt());
                $quote->delete();
            } catch (ModelNotFoundException $e) {
                $this->sendOutput($e->getMessage());
                return;
            }

            $this->sendOutput("%s [b]- [color=green]Removed successfully", $id);

            return true;
        } elseif ($this->info['text']->isInt()) {
            try {
                $quote = Quote::where('id', $this->info['text']->toInt())->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $this->sendOutput($e->getMessage());
                return;
            }

            $this->sendOutput("[%s]: %s [b]- Created %s", $quote->username, $quote->quote, $quote->created_at->diffForHumans());

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

        echo "Install, ";
    }

    public function update($version)
    {

        if(version_compare($version, $this->CONFIG['version'], '<')) {
            return true;
        }

        if(version_compare($version, '0.6', '<')) {
            echo "Update From 0.6, ";
        }

        if(version_compare($version, '0.8', '<')) {
            echo "Update From 0.8, ";
        }
    }
}
