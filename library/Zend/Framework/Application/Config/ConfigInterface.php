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
use Zend\Framework\Controller\ConfigInterface as Controllers;
use Zend\Framework\Event\Manager\ConfigInterface as Listeners;
use Zend\Framework\I18n\Translator\ConfigInterface as Translator;
use Zend\Framework\Route\ConfigInterface as Router;
use Zend\Framework\Service\ConfigInterface as Services;
use Zend\Framework\View\ConfigInterface as View;

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
     * @return Router
     */
    public function router();

    /**
     * @return Services
     */
    public function services();

    /**
     * @return Translator
     */
    public function translator();

    /**
     * @return View
     */
    public function view();
}
