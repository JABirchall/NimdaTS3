<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * $Id: Text.php 10/11/2013 11:35:22 scp@orilla $
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

use TeamSpeak3\Helper\StringHelper;
use TeamSpeak3\Node\AbstractNode;

/**
 * @class Text
 * @brief Renders nodes used in ASCII-based TeamSpeak 3 viewers.
 */
class Text implements IViewer
{
    /**
     * A pre-defined pattern used to display a node in a TeamSpeak 3 viewer.
     *
     * @var string
     */
    protected $pattern = "%0%1 %2\n";

    /**
     * Returns the code needed to display a node in a TeamSpeak 3 viewer.
     *
     * @param  AbstractNode $node
     * @param  array $siblings
     * @return StringHelper
     */
    public function fetchObject(AbstractNode $node, array $siblings = array())
    {
        $this->currObj = $node;
        $this->currSib = $siblings;

        $args = array(
            $this->getPrefix(),
            $this->getCorpusIcon(),
            $this->getCorpusName(),
        );

        return StringHelper::factory($this->pattern)->arg($args);
    }

    /**
     * Returns the ASCII string to display the prefix of the current node.
     *
     * @return string
     */
    protected function getPrefix()
    {
        $prefix = "";

        if (count($this->currSib)) {
            $last = array_pop($this->currSib);

            foreach ($this->currSib as $sibling) {
                $prefix .= ($sibling) ? "| " : "  ";
            }

            $prefix .= ($last) ? "\\-" : "|-";
        }

        return $prefix;
    }

    /**
     * Returns an ASCII string which can be used to display the status icon for a
     * AbstractNode object.
     *
     * @return string
     */
    protected function getCorpusIcon()
    {
        return $this->currObj->getSymbol();
    }

    /**
     * Returns a string for the current corpus element which contains the display name
     * for the current AbstractNode object.
     *
     * @return string
     */
    protected function getCorpusName()
    {
        return $this->currObj;
    }
}
