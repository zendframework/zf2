<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\I18n\Translator;

use Zend\Framework\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Mvc\I18n\Translator;

/**
 * Overrides the translator factory from the i18n component in order to
 * replace it with the bridge class from this namespace.
 */
class Factory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return Translator
     */
    public function createService(ServiceManager $sm)
    {
        // Configure the translator
        $config     = $sm->applicationConfig();
        $trConfig   = isset($config['translator']) ? $config['translator'] : array();
        $translator = Translator::factory($trConfig);
        return $translator;
    }
}
