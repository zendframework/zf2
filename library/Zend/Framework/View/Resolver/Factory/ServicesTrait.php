<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver\Factory;

use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;

trait ServicesTrait
{
    /**
     * @return TemplateMapResolver
     */
    public function map()
    {
        return $this->sm->get('View\Resolver\Map');
    }

    /**
     * @return TemplatePathStack
     */
    public function pathStack()
    {
        return $this->sm->get('View\Resolver\PathStack');
    }
}
