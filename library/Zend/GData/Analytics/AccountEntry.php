<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Analytics;

use Zend\GData;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 */
class AccountEntry extends GData\Entry
{
    protected $_accountId;
    protected $_accountName;
    protected $_profileId;
    protected $_webPropertyId;
    protected $_currency;
    protected $_timezone;
    protected $_tableId;
	protected $_profileName;
    protected $_goal;

    /**
     * @see Zend_Gdata_Entry::__construct()
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(GData\Analytics::$namespaces);
        parent::__construct($element);
    }

    /**
     * @param DOMElement $child
     * @return void
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        var_dump($absoluteNodeName);
        var_dump($this->lookupNamespace('ga') . ':' . 'goal');
        switch ($absoluteNodeName){
            case $this->lookupNamespace('analytics') . ':' . 'property';
                $property = new Extension\Property();
                $property->transferFromDOM($child);
                $this->{$property->getName()} = $property;
                break;
            case $this->lookupNamespace('analytics') . ':' . 'tableId';
                $tableId = new Extension\TableId();
                $tableId->transferFromDOM($child);
                $this->_tableId = $tableId;
                break;
        	case $this->lookupNamespace('ga') . ':' . 'goal';
	            $goal = new Extension\Goal();
	            $goal->transferFromDOM($child);
	            $this->_goal = $goal;
                break;
        	default:
            	parent::takeChildFromDOM($child);
                break;
        }
    }
}
