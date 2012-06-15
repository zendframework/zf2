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

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Name extends AbstractAnnotation
{
    protected $name;

    /**
     * Receive and process the contents of an annotation
     * 
     * @param  string $content 
     * @return void
     */
    public function initialize($content)
    {
        $name = $content;
        if ('"' === substr($content, 0, 1)) {
            $name = $this->parseJsonContent($content, __METHOD__);
        }
        if (!is_string($name)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define a string or a JSON string; received "%s"',
                __METHOD__,
                gettype($name)
            ));
        }
        $this->name = $name;
    }

    /**
     * Retrieve the name
     * 
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }
}

