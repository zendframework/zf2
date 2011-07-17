<?php
/**
 * @namespace
 */
namespace Zend\Db\ActiveRecord\Exception;
/**
 * @uses       \Zend\Db\Exception
 * @category   ZendX
 * @package    ZendX_Db
 * @subpackage ActiveRecord
 */
class NotFoundException extends Exception
{
	public $objectId,$className;

	public function __construct($objectId,$className){
		$this->objectId = $objectId;
		$this->className = $className;
	
		return parent::__construct('Cannot find '.$className.' with ID '.$objectId);
	}
}
