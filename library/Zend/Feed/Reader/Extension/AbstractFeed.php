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
use DOMDocument;
use DOMXPath;

/**
* @category Zend
* @package Zend_Feed_Reader
*/
abstract class AbstractFeed
{
    /**
     * Parsed feed data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Parsed feed data in the shape of a DOMDocument
     *
     * @var DOMDocument
     */
    protected $_domDocument = null;

    /**
     * The base XPath query used to retrieve feed data
     *
     * @var DOMXPath
     */
    protected $_xpath = null;

    /**
     * The XPath prefix
     *
     * @var string
     */
    protected $_xpathPrefix = '';

    /**
     * Constructor
     *
     * @param  Zend_Feed_Abstract $feed The source Zend_Feed object
     * @param  string $type Feed type
     * @return void
     */
    public function __construct(DOMDocument $dom, $type = null, DOMXPath $xpath = null)
    {
        $this->_domDocument = $dom;

        if ($type !== null) {
            $this->_data['type'] = $type;
        } else {
            $this->_data['type'] = Reader\Reader::detectType($dom);
        }

        if ($xpath !== null) {
            $this->_xpath = $xpath;
        } else {
            $this->_xpath = new DOMXPath($this->_domDocument);
        }

        $this->_registerNamespaces();
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
     * Get the Feed's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        $assumed = $this->getDomDocument()->encoding;
        return $assumed;
    }

    /**
     * Get the feed type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_data['type'];
    }


    /**
     * Return the feed as an array
     *
     * @return array
     */
    public function toArray() // untested
    {
        return $this->_data;
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
     * Get the DOMXPath object
     *
     * @return string
     */
    public function getXpath()
    {
        return $this->_xpath;
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
     * @return void
     */
    public function setXpathPrefix($prefix)
    {
        $this->_xpathPrefix = $prefix;
    }

    /**
     * Register the default namespaces for the current feed format
     */
    abstract protected function _registerNamespaces();
}
