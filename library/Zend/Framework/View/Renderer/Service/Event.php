<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer\Service;

use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\View\Exception\RuntimeException;
use Zend\View\Renderer\RendererInterface as Renderer;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     * @throws RuntimeException
     */
    public function __invoke(callable $listener, $options = null)
    {
        $response = $listener($this, $options);

        if (!$response instanceof Renderer) {
            throw new RuntimeException('No view renderer selected!');
        }

        return $response;
    }
}
