<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\Form;
use Zend\Form\Fieldset;

class NestedCollectionsForm extends Form
{
    public function __construct()
    {
        parent::__construct('nestedCollectionsForm');

        $this->add([
            'name' => 'testFieldset',
            'type' => 'Zend\Form\Fieldset',
            'options' => [
                 'use_as_base_fieldset' => true,
             ],
            'elements' => [
                [
                    'spec' => [
                        'name' => 'groups',
                        'type' => 'Zend\Form\Element\Collection',
                        'options' => [
                            'target_element' => [
                                'type' => 'Zend\Form\Fieldset',
                                'name' => 'group',
                                'elements' => [
                                    [
                                        'spec' => [
                                            'type' => 'Zend\Form\Element\Text',
                                            'name' => 'name',
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'type' => 'Zend\Form\Element\Collection',
                                            'name' => 'items',
                                            'options' => [
                                                'target_element' => [
                                                    'type' => 'Zend\Form\Fieldset',
                                                    'name' => 'item',
                                                    'elements' => [
                                                        [
                                                            'spec' => [
                                                                'type' => 'Zend\Form\Element\Text',
                                                                'name' => 'itemId',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->setValidationGroup([
            'testFieldset' => [
                'groups' => [
                    'name',
                    'items' => [
                        'itemId'
                    ]
                ],
            ]
        ]);
    }
}
