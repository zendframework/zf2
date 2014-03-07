<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver;

use Zend\View\Resolver\ResolverInterface as ViewResolver;

trait ServicesTrait
{
    /**
     * @return ViewResolver
     */
    public function viewResolver()
    {
        return $this->sm->get('View\Resolver');
    }

    /**
     * @param ViewResolver $resolver
     * @return self
     */
    public function setViewResolver(ViewResolver $resolver)
    {
        return $this->sm->add('View\Resolver', $resolver);
    }
}
