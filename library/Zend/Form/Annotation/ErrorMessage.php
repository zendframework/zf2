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
 * ErrorMessage annotation
 *
 * Allows providing an error message to seed the Input specification for a
 * given element. The content may either be a bare string or a JSON-encoded
 * string.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @Annotation
 */
class ErrorMessage
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) || !(is_string($data['value']) || is_array($data['value']))) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define a string or array; received "%s"',
                __METHOD__,
                gettype($data['value'])
            ));
        }

        $this->message = $data['value'];
    }

    /**
     * Retrieve the message
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
