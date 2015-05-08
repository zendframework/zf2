<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InputFilterAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param ServiceLocatorInterface $inputFilters
     * @param string                  $cName
     * @param string                  $rName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $inputFilters, $cName, $rName)
    {
        $services = $inputFilters->getServiceLocator();
        if (! $services instanceof ServiceLocatorInterface
            || ! $services->has('Config')
        ) {
            return false;
        }

        $config = $services->get('Config');
        if (!isset($config['input_filter_specs'][$rName])
            || !is_array($config['input_filter_specs'][$rName])
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param ServiceLocatorInterface $inputFilters
     * @param string                  $cName
     * @param string                  $rName
     * @return \Zend\InputFilter\InputFilterInterface
     */
    public function createServiceWithName(ServiceLocatorInterface $inputFilters, $cName, $rName)
    {
        $services  = $inputFilters->getServiceLocator();
        $factory   = $services->get('InputFilterFactory');
        $allConfig = $services->get('Config');
        $config    = $allConfig['input_filter_specs'][$rName];

        return $factory->createInputFilter($config);
    }
}
