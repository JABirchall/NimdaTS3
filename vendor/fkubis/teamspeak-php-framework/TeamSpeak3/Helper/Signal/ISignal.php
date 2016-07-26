<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: Interface.php 10/11/2013 11:35:21 scp@orilla $
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   TeamSpeak3
 * @version   1.1.23
 * @author    Sven 'ScP' Paulsen
 * @copyright Copyright (c) 2010 by Planet TeamSpeak. All rights reserved.
 */

namespace TeamSpeak3\Helper\Signal;

use TeamSpeak3\Adapter\AbstractAdapter;
use TeamSpeak3\Adapter\ServerQuery\Event;
use TeamSpeak3\Adapter\ServerQuery\Reply;
use TeamSpeak3\Node\Host;
use TeamSpeak3\Ts3Exception;
use TeamSpeak3\Adapter\FileTransfer;
use TeamSpeak3\Node\Server;

/**
 * @class ISignal
 * @brief Interface class describing the layout for Signal callbacks.
 */
interface ISignal
{
    /**
     * Possible callback for '<adapter>Connected' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("serverqueryConnected", array($object, "onConnect"));
     *   - Signal::getInstance()->subscribe("filetransferConnected", array($object, "onConnect"));
     *   - Signal::getInstance()->subscribe("blacklistConnected", array($object, "onConnect"));
     *   - Signal::getInstance()->subscribe("updateConnected", array($object, "onConnect"));
     *
     * @param  AbstractAdapter $adapter
     * @return void
     */
    public function onConnect(AbstractAdapter $adapter);

    /**
     * Possible callback for '<adapter>Disconnected' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("serverqueryDisconnected", array($object, "onDisconnect"));
     *   - Signal::getInstance()->subscribe("filetransferDisconnected", array($object, "onDisconnect"));
     *   - Signal::getInstance()->subscribe("blacklistDisconnected", array($object, "onDisconnect"));
     *   - Signal::getInstance()->subscribe("updateDisconnected", array($object, "onDisconnect"));
     *
     * @return void
     */
    public function onDisconnect();

    /**
     * Possible callback for 'serverqueryCommandStarted' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("serverqueryCommandStarted", array($object, "onCommandStarted"));
     *
     * @param  string $cmd
     * @return void
     */
    public function onCommandStarted($cmd);

    /**
     * Possible callback for 'serverqueryCommandFinished' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("serverqueryCommandFinished", array($object, "onCommandFinished"));
     *
     * @param  string $cmd
     * @param  Reply $reply
     * @return void
     */
    public function onCommandFinished($cmd, Reply $reply);

    /**
     * Possible callback for 'notifyEvent' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyEvent", array($object, "onEvent"));
     *
     * @param  Event $event
     * @param  Host $host
     * @return void
     */
    public function onEvent(Event $event, Host $host);

    /**
     * Possible callback for 'notifyError' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyError", array($object, "onError"));
     *
     * @param  Reply $reply
     * @return void
     */
    public function onError(Reply $reply);

    /**
     * Possible callback for 'notifyServerselected' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyServerselected", array($object, "onServerselected"));
     *
     * @param  Host $host
     * @return void
     */
    public function onServerselected(Host $host);

    /**
     * Possible callback for 'notifyServercreated' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyServercreated", array($object, "onServercreated"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServercreated(Host $host, $sid);

    /**
     * Possible callback for 'notifyServerdeleted' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyServerdeleted", array($object, "onServerdeleted"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServerdeleted(Host $host, $sid);

    /**
     * Possible callback for 'notifyServerstarted' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyServerstarted", array($object, "onServerstarted"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServerstarted(Host $host, $sid);

    /**
     * Possible callback for 'notifyServerstopped' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyServerstopped", array($object, "onServerstopped"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServerstopped(Host $host, $sid);

    /**
     * Possible callback for 'notifyServershutdown' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyServershutdown", array($object, "onServershutdown"));
     *
     * @param  Host $host
     * @return void
     */
    public function onServershutdown(Host $host);

    /**
     * Possible callback for 'notifyLogin' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyLogin", array($object, "onLogin"));
     *
     * @param  Host $host
     * @return void
     */
    public function onLogin(Host $host);

    /**
     * Possible callback for 'notifyLogout' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyLogout", array($object, "onLogout"));
     *
     * @param  Host $host
     * @return void
     */
    public function onLogout(Host $host);

    /**
     * Possible callback for 'notifyTokencreated' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("notifyTokencreated", array($object, "onTokencreated"));
     *
     * @param  Server $server
     * @param  string $token
     * @return void
     */
    public function onTokencreated(Server $server, $token);

    /**
     * Possible callback for 'filetransferHandshake' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("filetransferHandshake", array($object, "onFtHandshake"));
     *
     * @param  FileTransfer $adapter
     * @return void
     */
    public function onFtHandshake(FileTransfer $adapter);

    /**
     * Possible callback for 'filetransferUploadStarted' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("filetransferUploadStarted", array($object, "onFtUploadStarted"));
     *
     * @param  string $ftkey
     * @param  integer $seek
     * @param  integer $size
     * @return void
     */
    public function onFtUploadStarted($ftkey, $seek, $size);

    /**
     * Possible callback for 'filetransferUploadProgress' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("filetransferUploadProgress", array($object, "onFtUploadProgress"));
     *
     * @param  string $ftkey
     * @param  integer $seek
     * @param  integer $size
     * @return void
     */
    public function onFtUploadProgress($ftkey, $seek, $size);

    /**
     * Possible callback for 'filetransferUploadFinished' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("filetransferUploadFinished", array($object, "onFtUploadFinished"));
     *
     * @param  string $ftkey
     * @param  integer $seek
     * @param  integer $size
     * @return void
     */
    public function onFtUploadFinished($ftkey, $seek, $size);

    /**
     * Possible callback for 'filetransferDownloadStarted' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("filetransferDownloadStarted", array($object, "onFtDownloadStarted"));
     *
     * @param  string $ftkey
     * @param  integer $buff
     * @param  integer $size
     * @return void
     */
    public function onFtDownloadStarted($ftkey, $buff, $size);

    /**
     * Possible callback for 'filetransferDownloadProgress' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("filetransferDownloadProgress", array($object, "onFtDownloadProgress"));
     *
     * @param  string $ftkey
     * @param  integer $buff
     * @param  integer $size
     * @return void
     */
    public function onFtDownloadProgress($ftkey, $buff, $size);

    /**
     * Possible callback for 'filetransferDownloadFinished' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("filetransferDownloadFinished", array($object, "onFtDownloadFinished"));
     *
     * @param  string $ftkey
     * @param  integer $buff
     * @param  integer $size
     * @return void
     */
    public function onFtDownloadFinished($ftkey, $buff, $size);

    /**
     * Possible callback for '<adapter>DataRead' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("serverqueryDataRead", array($object, "onDebugDataRead"));
     *   - Signal::getInstance()->subscribe("filetransferDataRead", array($object, "onDebugDataRead"));
     *   - Signal::getInstance()->subscribe("blacklistDataRead", array($object, "onDebugDataRead"));
     *   - Signal::getInstance()->subscribe("updateDataRead", array($object, "onDebugDataRead"));
     *
     * @param  string $data
     * @return void
     */
    public function onDebugDataRead($data);

    /**
     * Possible callback for '<adapter>DataSend' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("serverqueryDataSend", array($object, "onDebugDataSend"));
     *   - Signal::getInstance()->subscribe("filetransferDataSend", array($object, "onDebugDataSend"));
     *   - Signal::getInstance()->subscribe("blacklistDataSend", array($object, "onDebugDataSend"));
     *   - Signal::getInstance()->subscribe("updateDataSend", array($object, "onDebugDataSend"));
     *
     * @param  string $data
     * @return void
     */
    public function onDebugDataSend($data);

    /**
     * Possible callback for '<adapter>WaitTimeout' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("serverqueryWaitTimeout", array($object, "onWaitTimeout"));
     *   - Signal::getInstance()->subscribe("filetransferWaitTimeout", array($object, "onWaitTimeout"));
     *   - Signal::getInstance()->subscribe("blacklistWaitTimeout", array($object, "onWaitTimeout"));
     *   - Signal::getInstance()->subscribe("updateWaitTimeout", array($object, "onWaitTimeout"));
     *
     * @param  integer $time
     * @param  AbstractAdapter $adapter
     * @return void
     */
    public function onWaitTimeout($time, AbstractAdapter $adapter);

    /**
     * Possible callback for 'errorException' signals.
     *
     * === Examples ===
     *   - Signal::getInstance()->subscribe("errorException", array($object, "onException"));
     *
     * @param  Ts3Exception $e
     * @return void
     */
    public function onException(Ts3Exception $e);
}
