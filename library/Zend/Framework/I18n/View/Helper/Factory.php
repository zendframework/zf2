<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\I18n\View\Helper;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Service\EventInterface as Request;
use Zend\Framework\Service\Factory\Factory as ServiceFactory;
use Zend\Framework\I18n\Translator\ServicesTrait as Translator;
use Zend\I18n\View\Helper\Translate as ViewHelper;

class Factory
    extends ServiceFactory
{
    /**
     *
     */
    use Config,
        Translator;

    /**
     * @param Request $request
     * @param array $options
     * @return Translator
     */
    public function __invoke(Request $request, array $options = [])
    {
        return (new ViewHelper)->setTranslator($this->translator());
    }
}
