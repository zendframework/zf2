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
 * @category  Zend
 * @package   Zend_Di
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Di\Instance\Dumper;



/**
 * Represents a scalar parameter, such as a string, array, null or integer
 *
 * @category  Zend
 * @package   Zend_Di
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ScalarParameter
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value a non-object value to be injected
     */
    public function __construct($value)
    {
        if (is_object($value)) {
            throw new InvalidArgumentException(
                'Scalar parameters don\'t support objects, value of type ' . get_class($value) . 'provided'
            );
        }
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getReferenceId()
    {
        return $this->value;
    }
}