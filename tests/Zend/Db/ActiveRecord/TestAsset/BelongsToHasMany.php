<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord\TestAsset;

class BelongsToHasMany extends \Zend\Db\ActiveRecord\AbstractActiveRecord
{
	static protected $_belongsTo = array(
		'parent' => array('\\ZendTest\\Db\\ActiveRecord\\TestAsset\\HasMany', 'parentId')
	);
}

