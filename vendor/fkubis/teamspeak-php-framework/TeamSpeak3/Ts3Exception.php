<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: Ts3Exception.php 10/11/2013 11:35:21 scp@orilla $
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

namespace TeamSpeak3;

use TeamSpeak3\Helper\Signal;
use TeamSpeak3\Helper\StringHelper;


/**
 * @class Ts3Exception
 * @brief Enhanced exception class for TeamSpeak3 objects.
 */
class Ts3Exception extends \Exception
{
    /**
     * Stores custom error messages.
     *
     * @var array
     */
    protected static $messages = array();

    /**
     * The Ts3Exception constructor.
     *
     * @param  string $mesg
     * @param  integer $code
     * @return Ts3Exception
     */
    public function __construct($mesg, $code = 0x00)
    {
        parent::__construct($mesg, $code);

        if (array_key_exists((int)$code, self::$messages)) {
            $this->message = $this->prepareCustomMessage(self::$messages[intval($code)]);
        }

        Signal::getInstance()->emit("errorException", $this);
    }

    /**
     * Prepares a custom error message by replacing pre-defined signs with given values.
     *
     * @param \Teamspeak3\Helper\StringHelper $mesg
     * @return \Teamspeak3\Helper\StringHelper
     */
    protected function prepareCustomMessage(StringHelper $mesg)
    {
        $args = array(
            "code" => $this->getCode(),
            "mesg" => $this->getMessage(),
            "line" => $this->getLine(),
            "file" => $this->getFile(),
        );

        return $mesg->arg($args)->toString();
    }

    /**
     * Registers a custom error message to $code.
     *
     * @param  integer $code
     * @param  string $mesg
     * @throws Ts3Exception
     * @return void
     */
    public static function registerCustomMessage($code, $mesg)
    {
        if (array_key_exists((int)$code, self::$messages)) {
            throw new self("custom message for code 0x" . strtoupper(dechex($code)) . " is already registered");
        }

        if (!is_string($mesg)) {
            throw new self("custom message for code 0x" . strtoupper(dechex($code)) . " must be a string");
        }

        self::$messages[(int)$code] = new StringHelper($mesg);
    }

    /**
     * Unregisters a custom error message from $code.
     *
     * @param  integer $code
     * @throws Ts3Exception
     * @return void
     */
    public static function unregisterCustomMessage($code)
    {
        if (!array_key_exists((int)$code, self::$messages)) {
            throw new self("custom message for code 0x" . strtoupper(dechex($code)) . " is not registered");
        }

        unset(self::$messages[intval($code)]);
    }

    /**
     * Returns the class from which the exception was thrown.
     *
     * @return string
     */
    public function getSender()
    {
        $trace = $this->getTrace();

        return (isset($trace[0]["class"])) ? $trace[0]["class"] : "{main}";
    }
}
