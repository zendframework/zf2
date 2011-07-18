<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord\TestAsset;

class StaticTableName extends \Zend\Db\ActiveRecord\AbstractActiveRecord
{
	// db table name is predefined in sublass definition
	protected static $_dbTable = 'app_stats';
}

