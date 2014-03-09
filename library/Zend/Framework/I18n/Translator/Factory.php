<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\I18n\Translator;

use Zend\Framework\Service\Factory\Factory as ServiceFactory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Mvc\I18n\Translator;

/**
 * Overrides the translator factory from the i18n component in order to
 * replace it with the bridge class from this namespace.
 */
class Factory
    extends ServiceFactory
{
    /**
     * @param Request $request
     * @param array $options
     * @return Translator
     */
    public function __invoke(Request $request, array $options = [])
    {
        // Configure the translator
        return Translator::factory($this->config()->translator()->config());
    }
}
