<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace Zend\XmlRpc\Generator;

/**
 * DOMDocument based implementation of a XML/RPC generator
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Generator
 */
class DomDocument extends AbstractGenerator
{
    /**
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * @var DOMNode
     */
    protected $_currentElement;

    /**
     * Start XML element
     *
     * @param string $name
     * @return void
     */
    protected function _openElement($name)
    {
        $newElement = $this->_dom->createElement($name);

        $this->_currentElement = $this->_currentElement->appendChild($newElement);
    }

    /**
     * Write XML text data into the currently opened XML element
     *
     * @param string $text
     */
    protected function _writeTextData($text)
    {
        $this->_currentElement->appendChild($this->_dom->createTextNode($text));
    }

    /**
     * Close an previously opened XML element
     *
     * Resets $_currentElement to the next parent node in the hierarchy
     *
     * @param string $name
     * @return void
     */
    protected function _closeElement($name)
    {
        if (isset($this->_currentElement->parentNode)) {
            $this->_currentElement = $this->_currentElement->parentNode;
        }
    }

    /**
     * Save XML as a string
     *
     * @return string
     */
    public function saveXml()
    {
        return $this->_dom->saveXml();
    }

    /**
     * Initializes internal objects
     *
     * @return void
     */
    protected function _init()
    {
        $this->_dom = new \DOMDocument('1.0', $this->_encoding);
        $this->_currentElement = $this->_dom;
    }
}
