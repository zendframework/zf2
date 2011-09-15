<?php
namespace ZendTest\Stdlib\Configuration;

use Zend\Stdlib\Configuration\AbstractConfiguration;

class ComponentAConfig extends AbstractConfiguration
{
	/**
	 * @var integer|null
	 */
	public $parameterA;

	/**
	 * @var string
	 */
	public $parameterB = 'defaultValue';

	/**
	 * @var integer|null
	 */
	public $parameterC = 15;

}
