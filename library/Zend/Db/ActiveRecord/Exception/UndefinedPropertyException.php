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
class UndefinedPropertyException extends Exception
{
	public function __construct($class, $property)
	{
		$this->message = "Undefined property: {$class}->{$property} in {$this->file}:{$this->line}";
		parent::__construct();
	}
};
