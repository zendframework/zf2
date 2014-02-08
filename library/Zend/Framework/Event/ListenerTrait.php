<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

trait ListenerTrait
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function target(EventInterface $event)
    {
        $target = $event->source();

        if (!isset($this->target) || !$target) {
            return true;
        }

        foreach((array) $this->target as $t) {
            if ($target == $t || $target instanceof $t || \is_subclass_of($target, $t)) {
                return true;
            }
        }

        return false;
    }
}
