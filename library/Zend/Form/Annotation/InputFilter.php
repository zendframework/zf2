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
 */

namespace Zend\Form\Annotation;

/**
 * InputFilter annotation
 *
 * Use this annotation to specify a specific input filter class to use with the 
 * form. The value should be a string indicating the fully qualified class name 
 * of the input filter to use.
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 */
class InputFilter extends AbstractStringAnnotation
{
    /**
     * Retrieve the input filter class
     * 
     * @return null|string
     */
    public function getInputFilter()
    {
        return $this->value;
    }
}
