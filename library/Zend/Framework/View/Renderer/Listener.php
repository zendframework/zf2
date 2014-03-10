<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\Event\EventInterface;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @param EventInterface $event
     * @return mixed|void
     */
    public function __invoke(EventInterface $event)
    {
        return $this->renderer();
    }
}
