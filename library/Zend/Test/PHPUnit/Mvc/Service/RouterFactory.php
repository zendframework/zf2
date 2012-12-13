<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace Zend\Test\PHPUnit\Mvc\Service;

use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class RouterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = null)
    {
        $config = $serviceLocator->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        return HttpRouter::factory($routerConfig);
    }
}
