<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\InputFilter\Factory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InputFilterFactoryFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $inputFilterManager = $services->get('InputFilterManager');

        $factory = new Factory($inputFilterManager);

        return $factory;
    }
}
