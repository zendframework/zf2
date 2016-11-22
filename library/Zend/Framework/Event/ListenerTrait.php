<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
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
        $source = $event->source();

        if (!isset($this->target) || !$source) {
            return true;
        }

        foreach((array) $this->target as $target) {
            if ($source == $target || $source instanceof $target || is_subclass_of($source, $target)) {
                return true;
            }
        }

        return false;
    }
}
