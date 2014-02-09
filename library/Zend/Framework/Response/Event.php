<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\Event\EventTrait as EventTrait;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @var string
     */
    protected $name = self::EVENT;

    /**
     * @param ListenerInterface $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(ListenerInterface $listener, $options = null)
    {
        $response = $listener->__invoke($this, $options);

        if ($response) {
            $this->stop();
        }

        return $response;
    }
}
