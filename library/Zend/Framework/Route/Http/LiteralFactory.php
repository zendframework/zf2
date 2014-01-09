<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\Mvc\Router\Exception;

class LiteralFactory
    extends FactoryListener
{
    /**
     * @param EventInterface $event
     * @return Literal
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(EventInterface $event)
    {
        $options = $event->options();

        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new Literal($options['route'], $options['defaults']);
    }
}
