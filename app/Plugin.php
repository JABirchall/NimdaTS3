<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25/07/2016
 * Time: 23:03
 */

namespace App;

/**
 * Class Plugin
 *
 * @package App
 */
use TeamSpeak3\Ts3Exception;

/**
 * Class Plugin
 *
 * @package App
 */
class Plugin
{
    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $author;
    /**
     * @var
     */
    protected $description;
    /**
     * @var
     */
    protected $version;
    /**
     * @var
     */
    protected $level;
    /**
     * @var mixed
     */
    public $triggers;
    /**
     * @var mixed
     */
    //protected $originalTriggers;
    /**
     * @var
     */
    protected $configFile;
    /**
     * @var
     */
    public $CONFIG;
    /**
     * @var
     */
    public $output;
    /**
     * @var
     */
    public $info;
    /**
     * @var
     */
    protected $teamSpeak3Bot;

    /**
     * Plugin constructor.
     *
     * @param $config
     * @param $teamSpeak3Bot
     */
    public function __construct($config, $teamSpeak3Bot)
    {

        $this->teamSpeak3Bot = $teamSpeak3Bot;
        foreach ($config as $name => $value) {
            switch ($name) {
                case "name":
                    $this->name = $value;
                    break;

                case "author":
                    $this->author = $value;
                    break;

                case "description":
                    $this->description = $value;
                    break;

                case "version":
                    $this->version = $value;
                    break;

                case "triggers":
                    $this->triggers = $this->sortByLengthDESC($value);
                    //$this->originalTriggers = $this->triggers;
                    break;

                case "configFile":
                    $this->configFile = $value;
                    break;

                default:
                    $this->CONFIG[$name] = $value;
                    break;
            }
        }

    }

    function trigger()
    {
        //$this->output = [];
        //$info_save = $this->info;
        //$this->info = $info_save;
        $this->isTriggered();
    }

    /**
     * @param $array
     *
     * @return mixed
     */
    static function sortByLengthASC($array)
    {
        usort($array, function($a, $b){
            return strlen($a)-strlen($b);
        });

        return $array;
    }

    /**
     * @param $array
     *
     * @return mixed
     */
    static function sortByLengthDESC($array)
    {
        usort($array, function($a, $b){
            return strlen($b)-strlen($a);
        });

        return $array;
    }

    /**
     * @param $text
     * @param $params
     */
    function sendOutput($text, ...$params)
    {
        $text = vsprintf($text, $params);

        try {
            $client = $this->teamSpeak3Bot->node->clientGetById(@$this->info['invokerid']?$this->info['invokerid']:$this->info['clid']);

            if (\App\TeamSpeak3Bot::$config['newLineNewMessage'] === false) {
               $client->message($text);
               $this->teamSpeak3Bot->printOutput($text);
            } else {
                $messages = explode("\n", $text);
                foreach ($messages as $message) {
                    if (!$message) {
                        continue;
                    }
                    $client->message($message);
                    $this->teamSpeak3Bot->printOutput($message);
                }
            }
        } catch (Ts3Exception $e) {
            $this->teamSpeak3Bot->onException($e);
        }
    }
}
