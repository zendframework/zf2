<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Media\Extension;

/**
 * Represents the media:thumbnail element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Media
 */
class MediaThumbnail extends \Zend\GData\Extension
{

    protected $_rootElement = 'thumbnail';
    protected $_rootNamespace = 'media';

    /**
     * @var string
     */
    protected $_url = null;

    /**
     * @var int
     */
    protected $_width = null;

    /**
     * @var int
     */
    protected $_height = null;

    /**
     * @var string
     */
    protected $_time = null;

    /**
     * Constructs a new MediaThumbnail element
     *
     * @param string $url
     * @param int $width
     * @param int $height
     * @param string $time
     */
    public function __construct($url = null, $width = null, $height = null,
            $time = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Media::$namespaces);
        parent::__construct();
        $this->_url = $url;
        $this->_width = $width;
        $this->_height = $height;
        $this->_time = $time ;
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_url !== null) {
            $element->setAttribute('url', $this->_url);
        }
        if ($this->_width !== null) {
            $element->setAttribute('width', $this->_width);
        }
        if ($this->_height !== null) {
            $element->setAttribute('height', $this->_height);
        }
        if ($this->_time !== null) {
            $element->setAttribute('time', $this->_time);
        }
        return $element;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'url':
            $this->_url = $attribute->nodeValue;
            break;
        case 'width':
            $this->_width = $attribute->nodeValue;
            break;
        case 'height':
            $this->_height = $attribute->nodeValue;
            break;
        case 'time':
            $this->_time = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $value
     * @return \Zend\GData\Media\Extension\MediaThumbnail Provides a fluent interface
     */
    public function setUrl($value)
    {
        $this->_url = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @param int $value
     * @return \Zend\GData\Media\Extension\MediaThumbnail Provides a fluent interface
     */
    public function setWidth($value)
    {
        $this->_width = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @param int $value
     * @return \Zend\GData\Media\Extension\MediaThumbnail Provides a fluent interface
     */
    public function setHeight($value)
    {
        $this->_height = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->_time;
    }

    /**
     * @param string $value
     * @return \Zend\GData\Media\Extension\MediaThumbnail Provides a fluent interface
     */
    public function setTime($value)
    {
        $this->_time = $value;
        return $this;
    }

}
