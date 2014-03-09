<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver\Factory;

use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\View\Resolver\TemplatePathStack;

class PathStackFactory
    extends Factory
{
    /**
     * @param Request $request
     * @param array $options
     * @return TemplatePathStack
     */
    public function __invoke(Request $request, array $options = [])
    {
        $config = $this->config()->view();

        $templatePathStack = new TemplatePathStack();

        if ($config->templatePathStack()) {
            $templatePathStack->addPaths($config->templatePathStack());
        }

        if ($config->defaultTemplateSuffix()) {
            $templatePathStack->setDefaultSuffix($config->defaultTemplateSuffix());
        }

        return $templatePathStack;
    }
}
