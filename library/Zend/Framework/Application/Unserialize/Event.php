<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Unserialize;

use Zend\Framework\Application\Config\ConfigInterface as Config;
use Zend\Framework\Event\EventTrait as EventTrait;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @param callable $listener
     * @param Config $config
     * @return mixed
     */
    public function __invoke(callable $listener, Config $config)
    {
        return $listener($this, $config);
    }
}
