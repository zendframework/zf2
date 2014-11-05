<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\TestAsset;

use SplSubject;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\SynchronizedFactoryInterface;
use Zend\ServiceManager\SynchronizerInterface;

class MultipleSynchronizedFactory implements FactoryInterface, SynchronizedFactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Foo();
    }

    /**
     * @param SplSubject|SynchronizerInterface $subject
     */
    public function update(SplSubject $subject)
    {
        
    }

    /**
     * Return a list of services that will be synchronized to the factory
     *
     * @return array
     */
    public function getServices()
    {
        return array('foo', 'bar');
    }
}
