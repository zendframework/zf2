<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Manager;

use Zend\Framework\Config\ConfigTrait;
use Zend\Framework\Controller\Manager\ConfigTrait as ControllerManager;
use Zend\Framework\Controller\Manager\ConfigInterface as ControllerConfigInterface;
use Zend\Framework\Event\Manager\ConfigTrait as EventManager;
use Zend\Framework\Event\Manager\ConfigInterface as EventConfigInterface;
use Zend\Framework\I18n\Translator\Manager\ConfigTrait as TranslatorManager;
use Zend\Framework\I18n\Translator\Manager\ConfigInterface as TranslatorConfigInterface;
use Zend\Framework\Response\Manager\ConfigInterface as ResponseConfigInterface;
use Zend\Framework\Response\Manager\ConfigTrait as ResponseManager;
use Zend\Framework\Route\Manager\ConfigInterface as RouteConfigInterface;
use Zend\Framework\Route\Manager\ConfigTrait as RouteManager;
use Zend\Framework\Service\Manager\ConfigInterface as ServiceConfigInterface;
use Zend\Framework\Service\Manager\ConfigTrait as ServiceManager;
use Zend\Framework\View\Manager\ConfigInterface as ViewConfigInterface;
use Zend\Framework\View\Manager\ConfigTrait as ViewManager;

class Config
    implements ConfigInterface,
               ControllerConfigInterface,
               EventConfigInterface,
               ResponseConfigInterface,
               RouteConfigInterface,
               ServiceConfigInterface,
               TranslatorConfigInterface,
               ViewConfigInterface
{
    /**
     *
     */
    use ConfigTrait,
        ControllerManager,
        EventManager,
        ResponseManager,
        RouteManager,
        ServiceManager,
        TranslatorManager,
        ViewManager;
}
