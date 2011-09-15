<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Stdlib;

use Serializable,
    Traversable;

/**
 * Interface for Configuration objects, used by Configurable components during
 * their instantiation. 
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Options extends Serializable
{
    /**
	 * Constructs an instance of Configuration from an array of values.
	 *
	 * @abstract
	 * @param array|Traversable		$config			Array with config values.
	 * @param bool 					$ignoreUnknown	Silently ignore unrecognized array keys
	 */
	 public function __construct($config = array(), $ignoreUnknown = true);

	/**
	 * This method handles processing arrays and Traversable objects. For each key in the array it will
	 * attempt to update the corresponding configuration values. This method should also allow and traverse
	 * objects implementing Traversable interface, such as ArrayObject or Zend\Config.
	 *
	 * @abstract
	 * @param array|Traversable		$config			Array with config values.
	 * @param bool 					$ignoreUnknown	Silently ignore unrecognized array keys
	 */
	 public function fromArray($config = array(), $ignoreUnknown = true);

    /**
	 * Returns an array with all Configuration values
	 *
	 * @abstract
	 * @return array
	 */
    public function toArray();

   

}
