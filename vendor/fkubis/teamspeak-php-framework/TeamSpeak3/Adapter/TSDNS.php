<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: TSDNS.php 10/11/2013 11:35:21 scp@orilla $
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

use TeamSpeak3\Helper\Profiler;
use TeamSpeak3\Helper\Signal;
use TeamSpeak3\Transport\AbstractTransport;
use TeamSpeak3\Ts3Exception;
use TeamSpeak3\Helper\StringHelper;

/**
 * @class TSDNS
 * @brief Provides methods to query a TSDNS server.
 */
class TSDNS extends AbstractAdapter
{
    /**
     * The TCP port number used by any TSDNS server.
     *
     * @var integer
     */
    protected $default_port = 41144;

    /**
     * Connects the AbstractTransport object and performs initial actions on the remote
     * server.
     *
     * @throws Ts3Exception
     * @return void
     */
    public function syn()
    {
        if (!isset($this->options["port"]) || empty($this->options["port"])) {
            $this->options["port"] = $this->default_port;
        }

        $this->initTransport($this->options);
        $this->transport->setAdapter($this);

        Profiler::init(spl_object_hash($this));

        Signal::getInstance()->emit("tsdnsConnected", $this);
    }

    /**
     * The FileTransfer destructor.
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
     * Queries the TSDNS server for a specified virtual hostname and returns the result.
     *
     * @param  string $tsdns
     * @throws Ts3Exception
     * @return String
     */
    public function resolve($tsdns)
    {
        $this->getTransport()->sendLine($tsdns);
        $repl = $this->getTransport()->readLine();
        $this->getTransport()->disconnect();

        if ($repl->section(":", 0)->toInt() == 404) {
            throw new Ts3Exception("unable to resolve TSDNS hostname (" . $tsdns . ")");
        }

        Signal::getInstance()->emit("tsdnsResolved", $tsdns, $repl);

        return $repl;
    }
}
