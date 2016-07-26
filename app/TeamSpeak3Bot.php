<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25/07/2016
 * Time: 22:12
 */

namespace App;

use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Helper\Signal;
use TeamSpeak3\Ts3Exception;
use TeamSpeak3\Adapter\ServerQuery\Event;

/**
 * Class TeamSpeak3Bot
 * @package App\TeamSpeak3Bot
 */
class TeamSpeak3Bot
{
    protected static $_version = '0.0.1';

    private $username;
    private $password;
    private $host;
    private $port;
    private $name;
    private $serverPort;
    public $node;
    public $channel;
    public $plugins;

    private static $_username;
    private static $_password;
    private static $_host;
    private static $_port;
    private static $_name;
    private static $_serverPort;
    private static $_instance;

    public $online = false;

    public function __construct($username = "serveradmin", $password = "", $host = "127.0.0.1", $port = "10011", $name = "DrBot", $serverPort = "9987")
    {
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->name = $name;
        $this->serverPort = $serverPort;
    }

    public function run()
    {
        try {
            $this->node = TeamSpeak3::factory("serverquery://{$this->username}:{$this->password}@{$this->host}:{$this->port}/?server_port={$this->serverPort}&blocking=0&nickname={$this->name}");
            $this->online = true;
        }catch (Ts3Exception $e)
        {
            $this->printOutput("Error {$e->getCode()}: {$e->getMessage()}");
            return;
        }

        $this->initializePlugins();
        $this->subscribe();
        $this->wait();
    }

    protected function subscribe()
    {
        Signal::getInstance()->subscribe("errorException", array($this, "onException"));
        Signal::getInstance()->subscribe("notifyTextmessage", [$this, "onMessage"]);
        Signal::getInstance()->subscribe("serverqueryConnected", [$this, "onConnect"]);
        Signal::getInstance()->subscribe("notifyEvent", [$this, "onEvent"]);
        $this->node->notifyRegister("server");
        $this->node->notifyRegister("channel");
        $this->node->notifyRegister("textserver");
        $this->node->notifyRegister("textchannel");
        $this->node->notifyRegister("textprivate");
        $this->printOutput("Events subscribed");
    }

    public function printOutput($output)
    {
        echo $output . PHP_EOL;
    }

    protected function wait()
    {
        $this->node->getAdapter()->wait();
    }

    public static function setOptions(Array $options = [])
    {
        Self::$_username = $options['username'];
        Self::$_password = $options['password'];
        Self::$_host = $options['host'];
        Self::$_port = $options['port'];
        Self::$_name = $options['name'];
        Self::$_serverPort = $options['serverPort'];
    }

    public static function getInstance()
    {
        if(Self::$_instance === null)
                self::$_instance = new Self(Self::$_username, Self::$_password, Self::$_host, Self::$_port, Self::$_name, Self::$_serverPort);


        return Self::$_instance;
    }

    public static function getNewInstance()
    {
        self::$_instance = new Self(Self::$_username, Self::$_password, Self::$_host, Self::$_port, Self::$_name, Self::$_serverPort);

        return Self::$_instance;
    }

    public static function getLastInstance()
    {
        if(Self::$_instance === null)
            self::$_instance = new Self(Self::$_username, Self::$_password, Self::$_host, Self::$_port, Self::$_name, Self::$_serverPort);


        return null;
    }

    private function initializePlugins() {
        $dir = opendir("config/plugins");

        while($file = readdir($dir)) {
            if(substr($file,-5) == ".conf")
                $this->loadPlugin($file);
        }

        closedir($dir);
    }

    private function loadPlugin($configFile) {
        $config = $this->parseConfigFile("config/plugins/".$configFile);
        $config['configFile'] = $configFile;

        if(!$config) {
            $this->printOutput("Plugin with config file '".$configFile."' has not been loaded cause it doesn't exist.");
            return false;
        }

        if(!isset($config['name'])) {
            $this->printOutput("Plugin with config file '".$configFile."' has not been loaded cause it has no name.");
            return false;
        }

        $this->printOutput("Loading Plugin [{$config['name']}] ...");
        require_once("plugins/".$config['name'].".php");

        $this->plugins[$config['name']] = new $config['name']($config, $this);
        $this->printOutput("OK");

        return true;
    }

    public function parseConfigFile($file) {
        if(!file_exists($file))
            return false;

        $array = [];
        $fp = fopen($file,"r");
        while($row = fgets($fp)) {
            $row = trim($row);
            if(preg_match('/^([A-Za-z0-9_]+?)\s+=\s+(.+?)$/',$row,$arr))
                $array[$arr[1]] = $arr[2];
        }

        fclose($fp);
        return $array;
    }

    public function joinChannel($channel) {
        try {
            $this->channel = $this->node->channelGetByName($channel);
            $this->node->clienMove($this->whoAmI(), $this->channel);
        }catch (Ts3Exception $e)
        {
            $this->printOutput("Error {$e->getCode()}: {$e->getMessage()}");
        }
    }

    public function whoAmI()
    {
        return $this->node->whoAmI();
    }

    public function kick($username, $reason = TeamSpeak3::KICK_CHANNEL, $message = "")
    {
        //try {
            $client = $this->node->clientGetByName($username);
            $client->kick($reason, $message);
        //}catch (Ts3Exception $e)
        //{
        //    $this->printOutput("Error {$e->getCode()}: {$e->getMessage()}");
        //}
    }

    public function sendPrivateMsg($target, $text)
    {

        $target->message($text);

    }

    public function sendServerMsg()
    {

    }

    public function sendChannelMsg()
    {

    }

    public function sendPoke()
    {

    }

    public function onConnect(AbstractAdapter $adapter)
    {
        $this->printOutput("Connected!");
    }

    public function onMessage(Event $event) {
        //var_dump($event->getData());

        $this->info['PRIVMSG'] = $event->getData();
        $info = $this->info['PRIVMSG'];

        foreach($this->plugins as $name => $config) {

            $this->plugins[$name]->info = $info;
            $this->plugins[$name]->onMessage();
            foreach($config->triggers as $trigger) {
                if($event["msg"]->startsWith($trigger)){
                //if(strtolower(substr($info['text'],0,strlen($trigger))) == strtolower($trigger)) {
                    $info['triggerUsed'] = $trigger;

                    $text = $event["msg"]->substr(strlen($trigger)+1);
                    $info['fullText'] = $event["msg"];
                    unset($info['text']);
                    if(!empty($text)) $info['text'] = $text;
                    $this->plugins[$name]->info = $info;
                    $this->plugins[$name]->trigger();
                    break;
                }
            }
        }
        $this->wait();
    }

    public function onEvent(Event $event)
    {
        //var_dump($event);
    }

    public function onException(Ts3Exception $e)
    {
        $this->printOutput("Error {$e->getCode()}: {$e->getMessage()}");
    }




}