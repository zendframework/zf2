<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\I18n\View\Helper;

use Zend\I18n\Exception;

use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Framework\Service\ServiceInterface;
use Zend\I18n\View\Helper\Translate as I18nTranslate;

/**
 * View helper for translating messages.
 */
class Translate
    extends I18nTranslate
    implements ServiceInterface
{
    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function __service(ServiceManager $sm)
    {
        $this->setTranslator($sm->service('Translator'));
    }
}
