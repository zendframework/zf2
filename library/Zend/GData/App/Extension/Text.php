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
 * @subpackage App
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\App\Extension;

use Zend\GData\App\Extension;

/**
 * Abstract class for data models that require only text and type-- such as:
 * title, summary, etc.
 *
 * @uses       \Zend\GData\App\Extension
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Text extends Extension
{

    protected $_rootElement = null;
    protected $_type = 'text';

    public function __construct($text = null, $type = 'text')
    {
        parent::__construct();
        $this->_text = $text;
        $this->_type = $type;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_type !== null) {
            $element->setAttribute('type', $this->_type);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'type':
            $this->_type = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /*
     * @return Zend\GData\App\Extension\Type
     */
    public function getType()
    {
        return $this->_type;
    }

    /*
     * @param string $value
     * @return Zend\GData\App\Extension\Text Provides a fluent interface
     */
    public function setType($value)
    {
        $this->_type = $value;
        return $this;
    }

}
