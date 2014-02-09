<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Callback;

use Zend\Framework\Event\EventInterface;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        return call_user_func_array($this->callback, array_merge([$event], (array) $options));
    }
}
