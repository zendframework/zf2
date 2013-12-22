<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Render;

use Exception;
use Zend\Framework\ApplicationServiceTrait as ServiceTrait;
use Zend\Framework\EventManager\Event as EventClass;
use Zend\Framework\Render\EventInterface as RenderInterface;

class ErrorEvent
    extends EventClass
    implements RenderInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @var string
     */
    protected $eventName = self::EVENT_RENDER_ERROR;

    /**
     * @var string
     */
    protected $error = 'error-exception';

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param $exception
     * @return $this
     */
    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
