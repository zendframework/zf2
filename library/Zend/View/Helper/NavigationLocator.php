<?php

namespace Zend\View\Helper;

use Zend\Navigation\NavigationLocator as Locator,
    Zend\View\Exception\BadMethodCallException;

/**
 * Proxy helper for retrieving navigational containers from the navigaton service.
 *
 * @uses       \Zend\View\Exception
 * @uses       \Zend\View\Helper\Navigation\AbstractHelper
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class NavigationLocator extends AbstractHelper
{
    /**
     * @var \Zend\Navigation\NavigationLocator
     */
    private $locator;

    /**
     * __invoke
     *
     * @access public
     * @throws BadMethodCallException if no locator is set.
     * @return \Zend\Navigation\Navigation
     */
    public function __invoke($name)
    {
        if ($this->getLocator()) {
            return $this->getLocator()->getContainer($name);
        } else {
            throw new BadMethodCallException('no navigation locator has been set');
        }
    }

    /**
     * Get locator
     *
     * @return \Zend\Navigation\NavigationLocator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Set locator
     *
     * @param \Zend\Navigation\NavigationLocator $locator
     * @return void
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
    }
}