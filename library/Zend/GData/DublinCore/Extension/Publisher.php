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
 * @subpackage DublinCore
 */

namespace Zend\GData\DublinCore\Extension;

/**
 * Entity responsible for making the resource available
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage DublinCore
 */
class Publisher extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'dc';
    protected $_rootElement = 'publisher';

    /**
     * Constructor for Zend_Gdata_DublinCore_Extension_Publisher which
     * Entity responsible for making the resource available
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($value = null)
    {
        $this->registerAllNamespaces(\Zend\GData\DublinCore::$namespaces);
        parent::__construct();
        $this->_text = $value;
    }

}
