<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord\TestAsset;

class NonPersistent extends \Zend\Db\ActiveRecord\AbstractActiveRecord
{
	// disable persistence
	protected static $_persistent = false;
}

