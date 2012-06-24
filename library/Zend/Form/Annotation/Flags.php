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
 * Flags annotation
 *
 * Allows passing flags to the form factory. These flags are used to indicate
 * metadata, and typically the priority (order) in which an element will be
 * included.
 *
 * The value should be a JSON-encoded object/associative array.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @Annotation
 */
class Flags
{
    /**
     * @var array
     */
    protected $flags;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) && is_array($data['value'])) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define an array; received "%s"',
                __METHOD__,
                gettype($data['value'])
            ));
        }

        $this->flags = $data['value'];
    }

    /**
     * Retrieve the flags
     *
     * @return null|array
     */
    public function getFlags()
    {
        return $this->flags;
    }
}
