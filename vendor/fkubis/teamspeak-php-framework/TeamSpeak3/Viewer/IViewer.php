<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: Interface.php 10/11/2013 11:35:22 scp@orilla $
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

namespace TeamSpeak3\Viewer;

use TeamSpeak3\Node\AbstractNode;

/**
 * @class IViewer
 * @brief Interface class describing a TeamSpeak 3 viewer.
 */
interface IViewer
{
    /**
     * Returns the code needed to display a node in a TeamSpeak 3 viewer.
     *
     * @param  AbstractNode $node
     * @param  array $siblings
     * @return string
     */
    public function fetchObject(AbstractNode $node, array $siblings = array());
}
