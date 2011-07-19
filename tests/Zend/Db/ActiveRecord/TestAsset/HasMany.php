<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord\TestAsset;

class HasMany extends \Zend\Db\ActiveRecord\AbstractActiveRecord
{
	static protected $_hasMany = array(
		'children' => array('\\ZendTest\\Db\\ActiveRecord\\TestAsset\\BelongsToHasMany', 'parentId')
	);
}

