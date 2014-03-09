<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Manager;

use Zend\Framework\Config\ConfigInterface as Config;
use Zend\Framework\Event\Config\ConfigInterface as Listeners;
use Zend\Framework\I18n\Translator\Config\ConfigInterface as Translator;
use Zend\Framework\Route\Config\ConfigInterface as Router;
use Zend\Framework\Service\Config\ConfigInterface as Services;

interface ConfigInterface
    extends Config
{
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
}
