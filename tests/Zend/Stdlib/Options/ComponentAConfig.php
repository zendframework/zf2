<?php
namespace ZendTest\Stdlib\Options;

use Zend\Stdlib\Options\AbstractOptions;

class ComponentAConfig extends AbstractOptions
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
