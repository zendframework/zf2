<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Template\Resolver;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\View\Resolver\TemplatePathStack;

class PathStackFactory
    extends FactoryListener
{
    /**
     *
     */
    use Config;

    /**
     * @param EventInterface $event
     * @return TemplatePathStack
     */
    public function __invoke(EventInterface $event)
    {
        $config = $this->applicationConfig();

        $templatePathStack = new TemplatePathStack();

        if (is_array($config) && isset($config['view_manager'])) {
            $config = $config['view_manager'];
            if (is_array($config)) {
                if (isset($config['template_path_stack'])) {
                    $templatePathStack->addPaths($config['template_path_stack']);
                }
                if (isset($config['default_template_suffix'])) {
                    $templatePathStack->setDefaultSuffix($config['default_template_suffix']);
                }
            }
        }

        return $templatePathStack;
    }
}
