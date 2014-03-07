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
use Zend\Framework\Controller\Config\ConfigInterface as Controllers;
use Zend\Framework\Event\Config\ConfigInterface as Listeners;
use Zend\Framework\I18n\Translator\ConfigInterface as Translator;
use Zend\Framework\Route\Config\ConfigInterface as Router;
use Zend\Framework\Service\Config\ConfigInterface as Services;
use Zend\Framework\View\Config\ConfigInterface as View;

class Config
    implements ConfigInterface
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
