<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord\TestAsset;

class DynamicTableName extends \Zend\Db\ActiveRecord\AbstractActiveRecord
{
	// there is no need to set up dbTable
	protected static $_dbTable = null;
	
	// db table name is determined with first WRITE or READ from the database
	protected static function _determineDbTable(){
		return 'app_stats_'.date('Y');	// table name changes each year
	}
}

