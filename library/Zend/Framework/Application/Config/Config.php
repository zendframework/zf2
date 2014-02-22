<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Config;

use Zend\Framework\Config\ConfigInterface as Serializable;
use Zend\Framework\Config\ConfigTrait as SerialConfigTrait;
use Zend\Framework\Controller\ConfigServiceInterface as ControllerConfigService;
use Zend\Framework\Controller\ConfigServiceTrait as ControllerConfig;
use Zend\Framework\Event\Manager\ConfigServiceInterface as ListenerConfigService;
use Zend\Framework\Event\Manager\ConfigServiceTrait as ListenerConfig;
use Zend\Framework\I18n\Translator\ConfigServiceInterface as TranslatorConfigService;
use Zend\Framework\I18n\Translator\ConfigServiceTrait as TranslatorConfig;
use Zend\Framework\Route\ConfigServiceInterface as RouterConfigService;
use Zend\Framework\Route\ConfigServiceTrait as RouterConfig;
use Zend\Framework\Service\ConfigServiceInterface as ServiceConfigService;
use Zend\Framework\Service\ConfigServiceTrait as ServiceConfig;
use Zend\Framework\View\ConfigServiceInterface as ViewConfigService;
use Zend\Framework\View\ConfigServiceTrait as ViewConfig;

class Config
    implements ConfigInterface,
               ControllerConfigService,
               ListenerConfigService,
               RouterConfigService,
               Serializable,
               ServiceConfigService,
               TranslatorConfigService,
               ViewConfigService
{
    /**
     *
     */
    use ControllerConfig,
        ListenerConfig,
        RouterConfig,
        SerialConfigTrait,
        ServiceConfig,
        TranslatorConfig,
        ViewConfig;
}
