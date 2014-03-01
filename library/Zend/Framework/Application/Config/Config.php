<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Config;

use Zend\Framework\Config\ConfigTrait;
use Zend\Framework\Controller\ConfigInterface as ControllersConfig;
use Zend\Framework\Event\Manager\ConfigInterface as ListenersConfig;
use Zend\Framework\I18n\Translator\ConfigInterface as TranslatorConfig;
use Zend\Framework\Route\ConfigInterface as RouterConfig;
use Zend\Framework\Service\ConfigInterface as ServicesConfig;
use Zend\Framework\View\ConfigInterface as ViewConfig;

class Config
    implements ConfigInterface
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @return ControllersConfig
     */
    public function controllers()
    {
        return $this->get('controllers');
    }

    /**
     * @return ListenersConfig
     */
    public function listeners()
    {
        return $this->get('listeners');
    }

    /**
     * @return RouterConfig
     */
    public function router()
    {
        return $this->get('router');
    }

    /**
     * @return ServicesConfig
     */
    public function services()
    {
        return $this->get('services');
    }

    /**
     * @return TranslatorConfig
     */
    public function translator()
    {
        return $this->get('translator');
    }

    /**
     * @return ViewConfig
     */
    public function view()
    {
        return $this->get('view');
    }
}
