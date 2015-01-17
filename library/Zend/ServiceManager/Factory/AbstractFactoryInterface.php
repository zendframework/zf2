<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\Factory;

/**
 * Interface for an abstract factory
 *
 * An abstract factory extends the factory interface, but also have an additional "canCreateService" method,
 * that is called to check if the abstract factory can create an instance of the given type. You should limit
 * the count of abstract factory to a minimum to keep good performance. Starting from ServiceManager v3, remember
 * that you can also attach multiple names to the same factory, which reduce the need for abstract factories
 */
interface AbstractFactoryInterface extends FactoryInterface
{
    /**
     * Can create the object?
     *
     * @param  string $name
     * @return bool
     */
    public function canCreateServiceWithName($name);
}