<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class FileInputFilterProviderFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => 'file',
            'name' => 'file_field',
            'options' => [
                'label' => 'File Label',
            ],
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'file_field' => [
                'type'     => 'Zend\InputFilter\FileInput',
                'filters'  => [
                    [
                        'name' => 'filerenameupload',
                        'options' => [
                            'target'    => __FILE__,
                            'randomize' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
