<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller\Plugin\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class TestFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->add([
            'name' => 'text',
            'type' => 'text',
        ]);

        $this->add([
            'name' => 'file',
            'type' => 'file',
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'text' => [
                'required' => true,
            ],
            'file' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'filerenameupload',
                        'options' => [
                            'target'    => __DIR__ . '/testfile.jpg',
                            'overwrite' => true,
                        ]
                    ]
                ],
            ],
        ];
    }
}
