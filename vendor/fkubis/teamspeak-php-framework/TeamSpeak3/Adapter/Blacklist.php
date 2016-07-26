<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: Blacklist.php 10/11/2013 11:35:21 scp@orilla $
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

use TeamSpeak3\Adapter\AbstractAdapter;
use TeamSpeak3\Helper\Profiler;
use TeamSpeak3\Helper\Signal;
use TeamSpeak3\Transport\AbstractTransport;
use TeamSpeak3\Ts3Exception;


/**
 * @class Blacklist
 * @brief Provides methods to check if an IP address is currently blacklisted.
 */
class Blacklist extends AbstractAdapter
{
    /**
     * The IPv4 address or FQDN of the TeamSpeak Systems update server.
     *
     * @var string
     */
    protected $default_host = "blacklist.teamspeak.com";

    /**
     * The UDP port number of the TeamSpeak Systems update server.
     *
     * @var integer
     */
    protected $default_port = 17385;

    /**
     * Stores an array containing the latest build numbers.
     *
     * @var array
     */
    protected $build_numbers = null;

    /**
     * Connects the AbstractTransport object and performs initial actions on the remote
     * server.
     *
     * @return void
     */
    public function syn()
    {
        if (!isset($this->options["host"]) || empty($this->options["host"])) {
            $this->options["host"] = $this->default_host;
        }
        if (!isset($this->options["port"]) || empty($this->options["port"])) {
            $this->options["port"] = $this->default_port;
        }

        $this->initTransport($this->options, "UDP");
        $this->transport->setAdapter($this);

        Profiler::init(spl_object_hash($this));

        Signal::getInstance()->emit("blacklistConnected", $this);
    }

    /**
     * The Blacklist destructor.
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->getTransport() instanceof AbstractTransport && $this->getTransport()->isConnected()) {
            $this->getTransport()->disconnect();
        }
    }

    /**
     * Returns TRUE if a specified $host IP address is currently blacklisted.
     *
     * @param  string $host
     * @throws Ts3Exception
     * @return boolean
     */
    public function isBlacklisted($host)
    {
        if (ip2long($host) === false) {
            $addr = gethostbyname($host);

            if ($addr == $host) {
                throw new Ts3Exception("unable to resolve IPv4 address (" . $host . ")");
            }

            $host = $addr;
        }

        $this->getTransport()->send("ip4:" . $host);
        $repl = $this->getTransport()->read(1);
        $this->getTransport()->disconnect();

        if (!count($repl)) {
            return false;
        }

        return ($repl->toInt()) ? false : true;
    }
}
