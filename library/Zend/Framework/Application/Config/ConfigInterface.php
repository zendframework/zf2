<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Config;

use Zend\Framework\Controller\ConfigInterface as ControllersConfig;
use Zend\Framework\Event\Manager\ConfigInterface as ListenersConfig;
use Zend\Framework\Service\ConfigInterface as ServicesConfig;
use Zend\Framework\View\ConfigInterface as ViewConfig;

interface ConfigInterface
{
    /**
     * @return ControllersConfig
     */
    public function controllers();

    /**
     * @return ListenersConfig
     */
    public function listeners();

    /**
     * @return array
     */
    public function router();

    /**
     * @return ServicesConfig
     */
    public function services();

    /**
     * @return array
     */
    public function translator();

    /**
     * @return ViewConfig
     */
    public function view();
}
