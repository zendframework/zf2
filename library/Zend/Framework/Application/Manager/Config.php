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
use Zend\Framework\Controller\Config\ConfigInterface as Controllers;
use Zend\Framework\Controller\Manager\ConfigInterface as ControllerConfigInterface;
use Zend\Framework\Event\Config\ConfigInterface as Listeners;
use Zend\Framework\I18n\Translator\Config\ConfigInterface as Translator;
use Zend\Framework\Response\Manager\ConfigInterface as ResponseConfigInterface;
use Zend\Framework\Route\Config\ConfigInterface as Router;
use Zend\Framework\Route\Manager\ConfigInterface as RouteConfigInterface;
use Zend\Framework\Service\Config\ConfigInterface as Services;
use Zend\Framework\View\Config\ConfigInterface as View;
use Zend\Framework\View\Manager\ConfigInterface as ViewConfigInterface;

class Config
    implements ConfigInterface,
               ControllerConfigInterface,
               ResponseConfigInterface,
               RouteConfigInterface,
               ViewConfigInterface
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @return Controllers
     */
    public function controllers()
    {
        return $this->get('controllers');
    }

    /**
     * @return Listeners
     */
    public function listeners()
    {
        return $this->get('listeners');
    }

    /**
     * @return Router
     */
    public function router()
    {
        return $this->get('router');
    }

    /**
     * @return Services
     */
    public function services()
    {
        return $this->get('services');
    }

    /**
     * @return Translator
     */
    public function translator()
    {
        return $this->get('translator');
    }

    /**
     * @return View
     */
    public function view()
    {
        return $this->get('view');
    }
}
