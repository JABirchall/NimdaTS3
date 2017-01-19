<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25/07/2016
 * Time: 22:12
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use TeamSpeak3\Helper\Convert;
use TeamSpeak3\Helper\Profiler\Timer;
use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Helper\Signal;
use TeamSpeak3\Ts3Exception;
use TeamSpeak3\Adapter\ServerQuery\Event;
use TeamSpeak3\Adapter\AbstractAdapter;
use Plugin\Models\Plugin;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class TeamSpeak3Bot
 *
 * @package App\TeamSpeak3Bot
 */
class TeamSpeak3Bot
{
    /**
     * @var string
     */
    const NIMDA_VERSION = '0.12.0';
    const NIMDA_TYPE = '-alpha';

    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $port;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $serverPort;
    /**
     * @var string
     */
    private $timeout;
    /**
     * @var
     */
    public $node;
    /**
     * @var
     */
    public $channel;
    /**
     * @var
     */
    private $plugins;

    public static $config;

    private $timer;
    /**
     * @var
     */
    private static $_username;
    /**
     * @var
     */
    private static $_password;
    /**
     * @var
     */
    private static $_host;
    /**
     * @var
     */
    private static $_port;
    /**
     * @var
     */
    private static $_name;
    /**
     * @var
     */
    private static $_serverPort;
    /**
     * @var
     */
    private static $_instance;
    /**
     * @var
     */
    private static $_timeout;

    /**
     * @var bool
     */
    public $online = false;

    private $database;

    private $lastEvent;

    public $carbon;

    /**
     * TeamSpeak3Bot constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $host
     * @param int $port
     * @param string $name
     * @param int $serverPort
     * @param int $timeout
     */
    public function __construct($username, $password, $host = "127.0.0.1", $port = 10011, $name = "Nimda", $serverPort = 9987, $timeout = 10)
    {
        $this->carbon = new Carbon;

       if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && posix_getuid() === 0 && !Self::$config['ignoreWarnings']) {
           $this->printOutput("[WARNING] Running Nimda as root is bad!");
           $this->printOutput("Start anyway? Y/N:", false);
           $response = rtrim(fgets(STDIN));
           if (strcasecmp($response,'y')) {
               $this->printOutput("Aborted.");
               exit;
           }
       }

       if($username === "serveradmin" && !Self::$config['ignoreWarnings']) {
           $this->printOutput("[WARNING] Running Nimda logged in as serveradmin is bad!");
           $this->printOutput("Start anyway? Y/N:", false);
           $response = rtrim(fgets(STDIN));
           if (strcasecmp($response,'y') ) {
               $this->printOutput("Aborted.");
               exit;
           }
       }

        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->name = $name;
        $this->serverPort = $serverPort;
        $this->timeout = $timeout;

        $this->timer = new Timer("start_up");
    }

    /**
     * Run the TeamSpeak3Bot instance
     */
    public function run()
    {
        $this->timer->start();

        $this->subscribe();
        try {
            $this->node = TeamSpeak3::factory("serverquery://{$this->username}:{$this->password}@{$this->host}:{$this->port}/?server_port={$this->serverPort}&blocking=0&nickname={$this->name}&timeout={$this->timeout}&no_query_clients=0");
        } catch (Ts3Exception $e) {
            $this->onException($e);

            return;
        }

        $this->database = new Database;
        $this->setup();
        $this->initializePlugins();
        $this->register();
        $this->timer->stop();

        $this->printOutput("Nimda version " . $this::NIMDA_VERSION . $this::NIMDA_TYPE . " Started in " . round($this->timer->getRuntime(), 3) . " seconds, Using " . Convert::bytes($this->timer->getMemUsage()) . " memory.");
        $this->timer = new Timer("runTime");
        $this->timer->start();
        $this->wait();
    }

    protected function subscribe()
    {
        Signal::getInstance()->subscribe("serverqueryWaitTimeout", [$this, "onTimeout"]);
        Signal::getInstance()->subscribe("serverqueryConnected", [$this, "onConnect"]);
        Signal::getInstance()->subscribe("notifyTextmessage", [$this, "onMessage"]);
        Signal::getInstance()->subscribe("notifyEvent", [$this, "onEvent"]);
        //Signal::getInstance()->subscribe("errorException", [$this, "onException"]);
        Signal::getInstance()->subscribe("serverqueryDisconnected", [$this, "onDisconnect"]);
    }

    protected function register()
    {
        $this->node->notifyRegister("textserver");
        $this->node->notifyRegister("textchannel");
        $this->node->notifyRegister("textprivate");
        $this->node->notifyRegister("server");
        $this->node->notifyRegister("channel");
    }

    /**
     * @param $output
     * @param $eol
     * @param $send
     */
    public function printOutput($output, $eol = true, $send = true)
    {
        if(!ob_get_contents()) {
            ob_start(null, 1024);
            printf("[%s]: %s ", $this->carbon->now()->toTimeString(), $output);
        } else {
            echo $output;
        }
        if ($eol) {
            echo PHP_EOL;
        }

        if($send) {
            ob_end_flush();
        }
    }

    /**
     * Wait for messages
     */
    protected function wait()
    {
        while ($this->online === true) {
            $this->node->getAdapter()->wait();
        }
    }

    /**
     * @param array $options
     */
    public static function setOptions(Array $options = [])
    {
        Self::$_username = $options['username'];
        Self::$_password = urlencode($options['password']);
        Self::$_host = $options['host'];
        Self::$_port = $options['port'];
        Self::$_name = urlencode($options['name']);
        Self::$_serverPort = $options['serverPort'];
        Self::$_timeout = $options['timeout'];
        Self::$config = $options['misc'];
    }

    /**
     * @return TeamSpeak3Bot
     */
    public static function getInstance()
    {
        if (Self::$_instance === null) {
            Self::$_instance = new Self(Self::$_username, Self::$_password, Self::$_host, Self::$_port, Self::$_name, Self::$_serverPort, Self::$_timeout);
        }

        return Self::$_instance;
    }

    /**
     * @return TeamSpeak3Bot
     */
    public static function getNewInstance()
    {
        Self::$_instance = new Self(Self::$_username, Self::$_password, Self::$_host, Self::$_port, Self::$_name, Self::$_serverPort, Self::$_timeout);

        return Self::$_instance;
    }


    private function setup()
    {
        if (!Manager::schema()->hasTable('plugins')) {
            Manager::schema()->create('plugins', function(Blueprint $table) {
                $table->increments('id');
                $table->text('name');
                $table->double('version');
                $table->timestamps();
            });

            Plugin::create([
                'name' => 'Nimda',
                'version' => Self::NIMDA_VERSION,
            ]);
        } else {
            $nimda = Plugin::where('name', 'Nimda')->first();
            if (version_compare($nimda->version, Self::NIMDA_VERSION, '<')) {
                $this->update($nimda->version);

                $nimda->update(['version' => Self::NIMDA_VERSION]);
            }
        }
    }

    private function update($version)
    {
        // TODO: Add bot update logic
    }

    private function initializePlugins()
    {
        foreach (glob('./config/plugins/*.json') as $file) {
            $this->loadPlugin($file);
        }
    }

    /**
     * @param $configFile
     *
     * @return bool
     */
    private function loadPlugin($configFile)
    {
        $config = $this->parseConfigFile($configFile);
        $config['configFile'] = $configFile;

        if (!$config) {
            $this->printOutput("Plugin with config file {$configFile} has not been loaded because it doesn't exist.");

            return false;
        } elseif (!isset($config['name'])) {
            $this->printOutput("Plugin with config file {$configFile} has not been loaded because it has no name.") ;

            return false;
        }


        $this->printOutput(sprintf("%- 80s %s", "Loading plugin [{$config['name']}] by {$config['author']} ", "::"), false, false);

        $config['class'] = \Plugin::class . '\\' . $config['name'];

        if (!class_exists($config['class'])) {
            $this->printOutput("Loading failed because class {$config['class']} doesn't exist.");

            return false;
        } elseif (!is_a($config['class'], \Plugin\PluginContract::class, true)) {
            $this->printOutput("Loading failed because class {$config['class']} does not implement [PluginContract].");

            return false;
        }

        $this->plugins[$config['name']] = new $config['class']($config, $this);

        if ($this->plugins[$config['name']] instanceof \Plugin\AdvancedPluginContract) {
            if (!Manager::schema()->hasTable($config['table'])) {
                $this->printOutput("Install, ", false, false);
                $this->plugins[$config['name']]->install();

                Plugin::create([
                    'name' => $config['name'],
                    'version' => $config['version'],
                ]);
            }

            $plugin = Plugin::where('name', $config['name'])->first();

            if (version_compare($plugin->version, $config['version'], '<')) {
                $this->plugins[$config['name']]->update($plugin->version);
                $plugin->update(['version' => $config['version']]);
            }
        }

        $this->printOutput("Success.");

        return true;
    }

    /**
     * @param $file
     *
     * @return array|bool
     */
    public function parseConfigFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $array = json_decode(file_get_contents($file), true);

        return $array;
    }

    /**
     * @param AbstractAdapter $adapter
     */
    public function onConnect(AbstractAdapter $adapter)
    {
        $this->online = true;
    }

    /**
     * @param Event $event
     */
    public function onMessage(Event $event)
    {
        $data = $event->getData();
        if (@$data['invokername'] == $this->name || @$data['invokeruid'] == 'serveradmin') {
            return;
        }

        foreach ($this->plugins as $name => $config) {
            if (@$config->CONFIG['event']) {
                continue;
            }
            foreach ($config->triggers as $trigger) {
                if ($trigger == 'event') {
                    break;
                }

                if ($event["msg"]->startsWith($trigger)) {
                	if ($data['targetmode'] == "1" && !isset($config->CONFIG['uids'])) {
                		break;
                	} else if ($data['targetmode'] == "1" && !in_array($data['invokeruid'],$config->CONFIG['uids'])) {
                		break;
                	}
                    $info =  $data;

                    $info['triggerUsed'] = $trigger;
                    $text = $event["msg"]->substr(strlen($trigger) + 1);
                    $info['fullText'] = $event["msg"];

                    if (!empty($text->toString())) {
                        $info['text'] = $text;
                    }

                    $this->plugins[$name]->info = $info;
                    $this->plugins[$name]->trigger();
                    break;
                }
            }
        }
    }

    /**
     * @param Event $event
     */
    public function onEvent(Event $event)
    {
        $data = $event->getData();
        if (@$data['client_type'] == 1) {
            return;
        } elseif ($this->lastEvent && $event->getMessage()->contains($this->lastEvent)) {
            return;
        }

        $this->lastEvent = $event->getMessage()->toString();
        $this->updateList();

        foreach ($this->plugins as $name => $config) {
            if (!@$config->CONFIG['event']) {
                continue;
            }

            foreach ($config->triggers as $trigger) {
                if ($event->getType() != $config->CONFIG['event']) {
                    break;
                }

                $this->plugins[$name]->info = $data;

                $this->plugins[$name]->trigger();
                break;
            }
        }
    }

    protected function updateList()
    {
        $this->node->clientListReset();
        $this->node->channelListReset();
    }

    public function onTimeout($time, AbstractAdapter $adapter)
    {
        if ($adapter->getQueryLastTimestamp() < $this->carbon->now()->subSeconds(120)->timestamp) {
            $adapter->request("clientupdate");
            $this->updateList();
        }

        if(Self::$config['debug'] === true) {
            $this->printOutput("Nimda runtime: " . Convert::seconds($this->timer->getRuntime()) . ", Using " . Convert::bytes($this->timer->getMemUsage()) . " memory.");
        }
    }

    public function onDisconnect()
    {
    	$this->online = false;
        $this->timer->stop();
        $this->printOutput("Nimda finished total runtime: " . Convert::seconds($this->timer->getRuntime()) . " seconds, Using " . Convert::bytes($this->timer->getMemUsage()) . " memory.");
    }

    /**
     * @param Ts3Exception $e
     */
    public function onException(Ts3Exception $e)
    {
        $this->printOutput("Error {$e->getCode()}: {$e->getMessage()}");
    }

}
