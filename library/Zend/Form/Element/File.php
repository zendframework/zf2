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
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Element;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\Exception;
use Zend\InputFilter\InputProviderInterface;
use Zend\Stdlib\ErrorHandler;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class File extends Element implements InputProviderInterface, ElementPrepareAwareInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'file',
    );

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param  Form $form
     * @return mixed
     */
    public function prepareElement(Form $form)
    {
        // Ensure the form is using correct enctype
        $form->setAttribute('enctype', 'multipart/form-data');
    }

    /**
     * @return boolean|string
     */
    public function getFileName()
    {
        $value  = $this->getValue();
        if (!isset($value['error']) || $value['error'] !== UPLOAD_ERR_OK
            || !isset($value['tmp_name']) || !is_uploaded_file($value['tmp_name'])
        ) {
            return false;
        }

        return $value['tmp_name'];
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'type'       => 'Zend\InputFilter\FileInput',
            'name'       => $this->getName(),
            'required'   => false,
            'validators' => array(
                array('name' => 'fileupload'),
            ),
        );
    }
}
