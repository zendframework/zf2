<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Exception;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param EventInterface $event
     * @return mixed|void
     * @throws Exception
     */
    public function service(EventInterface $event)
    {
        throw new Exception('Missing service method for ' . get_class($this));
    }
}
