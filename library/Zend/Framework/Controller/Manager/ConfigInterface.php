<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Config\ConfigInterface as Config;
use Zend\Framework\Controller\Config\ConfigInterface as Controllers;
use Zend\Framework\Event\Config\ConfigInterface as Listeners;
use Zend\Framework\Service\Config\ConfigInterface as Services;

interface ConfigInterface
    extends Config
{
    /**
     * @return Controllers
     */
    public function controllers();

    /**
     * @return Listeners
     */
    public function listeners();

    /**
     * @return Services
     */
    public function services();
}
