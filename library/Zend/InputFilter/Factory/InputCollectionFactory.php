<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\Factory;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputCollection;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory used to create an input collection
 */
class InputCollectionFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new InputCollection(
            new \Zend\Filter\FilterChain(),
            new \Zend\Validator\ValidatorChain(),
            new Factory($serviceLocator)
        );
    }
}
