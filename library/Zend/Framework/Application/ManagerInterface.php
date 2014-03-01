<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\EventInterface;

use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;

interface ManagerInterface
    extends ServiceManagerInterface
{
    /**
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function run($event = Event::EVENT, $options = null);

    /**
     * @param EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function __invoke(EventInterface $event, $options = null);
}
