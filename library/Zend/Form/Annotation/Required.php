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
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

use Zend\Form\Exception;

/**
 * Required annotation
 *
 * Use this annotation to specify the value of the "required" flag for a given
 * input. Since the flag defaults to "true", this will typically be used to
 * "unset" the flag (e.g., "@Annotation\Required(false)"). Any boolean value
 * understood by \Zend\Filter\Boolean is allowed as the content; if the value
 * is JSON-encoded, it will be decoded before being passed to the filter.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @Annotation
 */
class Required
{
    /**
     * @var bool
     */
    protected $value = true;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) && !is_bool($data['value'])) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define a boolean; received "%s"',
                __METHOD__,
                gettype($data['value'])
            ));
        }

        $this->value = $data['value'];
    }

    /**
     * Get value of required flag
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->value;
    }
}

