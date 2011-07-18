<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord\TestAsset;

class DefaultCache extends \Zend\Db\ActiveRecord\AbstractActiveRecord
{
	// use "othercache" cache adapter stored in \Zend\Registry
	protected static $_defaultCache = 'othercache';
}

