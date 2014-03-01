<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Config;

use Zend\Framework\Config\ConfigInterface as Config;
use Zend\Framework\Controller\ConfigInterface as ControllersConfig;
use Zend\Framework\Event\Manager\ConfigInterface as ListenersConfig;
use Zend\Framework\I18n\Translator\ConfigInterface as TranslatorConfig;
use Zend\Framework\Route\ConfigInterface as RouterConfig;
use Zend\Framework\Service\ConfigInterface as ServiceConfig;
use Zend\Framework\View\ConfigInterface as ViewConfig;

interface ConfigInterface
    extends Config
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
     * @return RouterConfig
     */
    public function router();

    /**
     * @return ServiceConfig
     */
    public function services();

    /**
     * @return TranslatorConfig
     */
    public function translator();

    /**
     * @return ViewConfig
     */
    public function view();
}
