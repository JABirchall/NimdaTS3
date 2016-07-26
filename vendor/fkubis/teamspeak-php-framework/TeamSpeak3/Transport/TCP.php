<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: TCP.php 10/11/2013 11:35:22 scp@orilla $
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

namespace TeamSpeak3\Transport;

use TeamSpeak3\Helper\Signal;
use TeamSpeak3\Helper\StringHelper;
use TeamSpeak3\Ts3Exception;

/**
 * @class TCP
 * @brief Class for connecting to a remote server through TCP.
 */
class TCP extends AbstractTransport
{
    /**
     * Connects to a remote server.
     *
     * @throws Ts3Exception
     * @return void
     */
    public function connect()
    {
        if ($this->stream !== null) {
            return;
        }

        $host = strval($this->config["host"]);
        $port = strval($this->config["port"]);

        $address = "tcp://" . $host . ":" . $port;
        $timeout = intval($this->config["timeout"]);

        $this->stream = @stream_socket_client($address, $errno, $errstr, $timeout);

        if ($this->stream === false) {
            throw new Ts3Exception(
                StringHelper::factory($errstr)->toUtf8()->toString(),
                $errno
            );
        }

        @stream_set_timeout($this->stream, $timeout);
        @stream_set_blocking($this->stream, $this->config["blocking"] ? 1 : 0);
    }

    /**
     * Disconnects from a remote server.
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->stream === null) {
            return;
        }

        $this->stream = null;

        Signal::getInstance()->emit(strtolower($this->getAdapterType()) . "Disconnected");
    }

    /**
     * Reads data from the stream.
     *
     * @param  integer $length
     * @throws Ts3Exception
     * @return String
     */
    public function read($length = 4096)
    {
        $this->connect();
        $this->waitForReadyRead();

        $data = @stream_get_contents($this->stream, $length);

        Signal::getInstance()->emit(strtolower($this->getAdapterType()) . "DataRead", $data);

        if ($data === false) {
            throw new Ts3Exception(
                "connection to server '" . $this->config["host"] . ":" . $this->config["port"] . "' lost"
            );
        }

        return new StringHelper($data);
    }

    /**
     * Reads a single line of data from the stream.
     *
     * @param  string $token
     * @throws Ts3Exception
     * @return String
     */
    public function readLine($token = "\n")
    {
        $this->connect();

        $line = StringHelper::factory("");

        while (!$line->endsWith($token)) {
            $this->waitForReadyRead();

            $data = @fgets($this->stream, 4096);

            Signal::getInstance()->emit(strtolower($this->getAdapterType()) . "DataRead", $data);

            if ($data === false) {
                if ($line->count()) {
                    $line->append($token);
                } else {
                    throw new Ts3Exception(
                        "connection to server '" . $this->config["host"] . ":" . $this->config["port"] . "' lost"
                    );
                }
            } else {
                $line->append($data);
            }
        }

        return $line->trim();
    }

    /**
     * Writes data to the stream.
     *
     * @param  string $data
     * @return void
     */
    public function send($data)
    {
        $this->connect();

        @stream_socket_sendto($this->stream, $data);

        Signal::getInstance()->emit(strtolower($this->getAdapterType()) . "DataSend", $data);
    }

    /**
     * Writes a line of data to the stream.
     *
     * @param  string $data
     * @param  string $separator
     * @return void
     */
    public function sendLine($data, $separator = "\n")
    {
        $size = strlen($data);
        $pack = 4096;

        for ($seek = 0; $seek < $size;) {
            $rest = $size - $seek;
            $pack = $rest < $pack ? $rest : $pack;
            $buff = substr($data, $seek, $pack);
            $seek = $seek + $pack;

            if ($seek >= $size) {
                $buff .= $separator;
            }

            $this->send($buff);
        }
    }
}
