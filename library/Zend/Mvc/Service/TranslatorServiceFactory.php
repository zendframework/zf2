<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\I18n\Translator\TranslatorServiceFactory as I18nTranslatorServiceFactory;
use Zend\Mvc\I18n\Translator;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;

/**
 * Overrides the translator factory from the i18n component in order to
 * replace it with the bridge class from this namespace.
 */
class TranslatorServiceFactory extends I18nTranslatorServiceFactory
{
    public function createService(ServiceManager $sm)
    {
        // Configure the translator
        $config     = $sm->get(new ServiceRequest('ApplicationConfig'));
        $trConfig   = isset($config['translator']) ? $config['translator'] : array();
        $translator = Translator::factory($trConfig);
        return $translator;
    }
}
