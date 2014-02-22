<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch\Error;

use Exception;
use Zend\Framework\Event\EventTrait as EventTrait;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception  = $exception;
    }

    /**
     * @return Exception
     */
    public function exception()
    {
        return $this->exception;
    }

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        list($request, $response) = $options;
        return $listener($this, $request, $response);
    }
}
