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
use Zend\Stdlib\Hydrator\ArraySerializable;
use ZendTest\Form\TestAsset\Entity\Orphan;

class OrphansFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct( $name = null, $options = [] )
    {
        parent::__construct( $name, $options );

        $this->setHydrator(new ArraySerializable())
                ->setObject(new Orphan());

        $this->add([
                        'name' => 'name',
                        'options' => ['label' => 'Name field'],
                   ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => false,
                'filters' => [],
                'validators' => [],
            ]
        ];
    }
}
