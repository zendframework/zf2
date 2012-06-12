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
 * @subpackage Analytics
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\GData\Analytics;

use Zend\GData\Analytics;

/**
 * Describes an entry in a feed of collections
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AccountEntry extends \Zend\Gdata\Entry
{
    /**
     * @var string
     */
	protected $_accountId;
	/**
	 * @var string
	 */
	protected $_accountName;
	/**
	 * @var string
	 */
	protected $_profileId;
	/**
	 * @var string
	 */
	protected $_webPropertyId;
	/**
	 * @var string
	 */
	protected $_currency;
	/**
	 * @var string
	 */
	protected $_timezone;
	/**
	 * @var string
	 */
	protected $_tableId;

	/**
	 * @see Zend_Gdata_Entry::__construct()
	 */
	public function __construct($element = null)
    {
        $this->registerAllNamespaces(Analytics::$namespaces);
        parent::__construct($element);
    }

    /**
     * @param DOMElement $child
     * @return void
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName){
        	case $this->lookupNamespace('ga') . ':' . 'property';
	            $property = new Extension\Property();
	            $property->transferFromDOM($child);
	            $this->{$property->getName()} = $property;
            break;
        	case $this->lookupNamespace('ga') . ':' . 'tableId';
	            $tableId = new Extension\TableId();
	            $tableId->transferFromDOM($child);
	            $this->_tableId = $tableId;
            break;
        	default:
            	parent::takeChildFromDOM($child);
            break;
        }
    }
}
