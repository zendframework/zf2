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
 * @package    Reader\Reader
 */

namespace Zend\Feed\Reader\Extension;

use Zend\Feed\Reader;
use DOMXPath;
use DOMDocument;
use DOMElement;

/**
* @category Zend
* @package Reader\Reader
*/
abstract class AbstractEntry
{
    /**
     * Feed entry data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * DOM document object
     *
     * @var DOMDocument
     */
    protected $_domDocument = null;

    /**
     * Entry instance
     *
     * @var Zend_Feed_Entry_Abstract
     */
    protected $_entry = null;

    /**
     * Pointer to the current entry
     *
     * @var int
     */
    protected $_entryKey = 0;

    /**
     * XPath object
     *
     * @var DOMXPath
     */
    protected $_xpath = null;

    /**
     * XPath query
     *
     * @var string
     */
    protected $_xpathPrefix = '';

    /**
     * Constructor
     *
     * @param  Zend_Feed_Entry_Abstract $entry
     * @param  int $entryKey
     * @param  string $type
     * @return void
     */
    public function __construct(DOMElement $entry, $entryKey, $type = null)
    {
        $this->_entry       = $entry;
        $this->_entryKey    = $entryKey;
        $this->_domDocument = $entry->ownerDocument;

        if ($type !== null) {
            $this->_data['type'] = $type;
        } else {
            $this->_data['type'] = Reader\Reader::detectType($entry->ownerDocument, true);
        }
        // set the XPath query prefix for the entry being queried
        if ($this->getType() == Reader\Reader::TYPE_RSS_10
            || $this->getType() == Reader\Reader::TYPE_RSS_090
        ) {
            $this->setXpathPrefix('//rss:item[' . ($this->_entryKey+1) . ']');
        } elseif ($this->getType() == Reader\Reader::TYPE_ATOM_10
                  || $this->getType() == Reader\Reader::TYPE_ATOM_03
        ) {
            $this->setXpathPrefix('//atom:entry[' . ($this->_entryKey+1) . ']');
        } else {
            $this->setXpathPrefix('//item[' . ($this->_entryKey+1) . ']');
        }
    }

    /**
     * Get the DOM
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->_domDocument;
    }

    /**
     * Get the Entry's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        $assumed = $this->getDomDocument()->encoding;
        return $assumed;
    }

    /**
     * Get the entry type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_data['type'];
    }

    /**
     * Set the XPath query
     *
     * @param  DOMXPath $xpath
     * @return Reader\Reader_Extension_EntryAbstract
     */
    public function setXpath(DOMXPath $xpath)
    {
        $this->_xpath = $xpath;
        $this->_registerNamespaces();
        return $this;
    }

    /**
     * Get the XPath query object
     *
     * @return DOMXPath
     */
    public function getXpath()
    {
        if (!$this->_xpath) {
            $this->setXpath(new DOMXPath($this->getDomDocument()));
        }
        return $this->_xpath;
    }

    /**
     * Serialize the entry to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Get the XPath prefix
     *
     * @return string
     */
    public function getXpathPrefix()
    {
        return $this->_xpathPrefix;
    }

    /**
     * Set the XPath prefix
     *
     * @param  string $prefix
     * @return Reader\Reader_Extension_EntryAbstract
     */
    public function setXpathPrefix($prefix)
    {
        $this->_xpathPrefix = $prefix;
        return $this;
    }

    /**
     * Register XML namespaces
     *
     * @return void
     */
    protected abstract function _registerNamespaces();
}
