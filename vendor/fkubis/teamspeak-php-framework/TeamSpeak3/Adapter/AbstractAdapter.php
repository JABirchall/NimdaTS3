<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: AbstractAdapter.php 10/11/2013 11:35:21 scp@orilla $
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

namespace TeamSpeak3\Adapter;

use TeamSpeak3\Ts3Exception;
use TeamSpeak3\Transport\TCP;
use TeamSpeak3\Transport\UDP;
use TeamSpeak3\Transport\AbstractTransport;
use TeamSpeak3\Helper\Profiler;
use TeamSpeak3\Helper\Profiler\Timer;

/**
 * @class AbstractAdapter
 * @brief Provides low-level methods for concrete adapters to communicate with a TeamSpeak 3 Server.
 */
abstract class AbstractAdapter
{
    /**
     * Stores user-provided options.
     *
     * @var array
     */
    protected $options = null;

    /**
     * Stores an AbstractTransport object.
     *
     * @var AbstractTransport
     */
    protected $transport = null;

    /**
     * The AbstractAdapter constructor.
     *
     * @param  array $options
     * @return AbstractAdapter
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        if ($this->transport === null) {
            $this->syn();
        }
    }

    /**
     * The AbstractAdapter destructor.
     *
     * @return void
     */
    abstract public function __destruct();

    /**
     * Connects the AbstractTransport object and performs initial actions on the remote
     * server.
     *
     * @throws Ts3Exception
     * @return void
     */
    abstract protected function syn();

    /**
     * Commit pending data.
     *
     * @return array
     */
    public function __sleep()
    {
        return array("options");
    }

    /**
     * Reconnects to the remote server.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->syn();
    }

    /**
     * Returns the profiler timer used for this connection adapter.
     *
     * @return Timer
     */
    public function getProfiler()
    {
        return Profiler::get(spl_object_hash($this));
    }

    /**
     * Returns the transport object used for this connection adapter.
     *
     * @return AbstractTransport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Loads the transport object object used for the connection adapter and passes a given set
     * of options.
     *
     * @param  array $options
     * @param  string $transport
     * @throws Ts3Exception
     * @return void
     */
    protected function initTransport($options, $transport = "TCP")
    {
        if (!is_array($options)) {
            throw new Ts3Exception("transport parameters must provided in an array");
        }
        if($transport == "TCP")
            $this->transport = new TCP($options);
        else
            $this->transport = new UDP($options);
    }

    /**
     * Returns the hostname or IPv4 address the underlying AbstractTransport object
     * is connected to.
     *
     * @return string
     */
    public function getTransportHost()
    {
        return $this->getTransport()->getConfig("host", "0.0.0.0");
    }

    /**
     * Returns the port number of the server the underlying AbstractTransport object
     * is connected to.
     *
     * @return string
     */
    public function getTransportPort()
    {
        return $this->getTransport()->getConfig("port", "0");
    }
}
