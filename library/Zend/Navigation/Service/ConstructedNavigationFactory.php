<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage Exception
 */

namespace Zend\Navigation\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Constructed factory to set pages during construction.
 *
 * @category  Zend
 * @package   Zend_Navigation
 */
class ConstructedNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @param string|\Zend\Config\Config|array $config
     */
    public function __construct($config)
    {
        $this->pages = $this->getPagesFromConfig($config);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array|null|\Zend\Config\Config
     */
    public function getPages(ServiceLocatorInterface $serviceLocator)
    {
        return $this->pages;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'constructed';
    }
}
