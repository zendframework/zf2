<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */

namespace Zend\GData\YouTube;

use Zend\GData\YouTube;

/**
 * The YouTube video playlist flavor of an Atom Feed with media support
 * Represents a list of videos contained in a playlist.  Each entry in this
 * feed represents an individual video.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class PlaylistVideoFeed extends \Zend\GData\Media\Feed
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\YouTube\PlaylistVideoEntry';

    /**
     * Creates a Play Video feed, representing a list of videos contained
     * within a single playlist.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(YouTube::$namespaces);
        parent::__construct($element);
    }

}
