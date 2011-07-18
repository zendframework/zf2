<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord\TestAsset;

class DefaultDb extends \Zend\Db\ActiveRecord\AbstractActiveRecord
{
	// use "otherdb" db adapter stored in \Zend\Registry
	protected static $_defaultDb = 'otherdb';
}

