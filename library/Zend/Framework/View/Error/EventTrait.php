<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Error;

use Exception;
use Zend\Framework\Service\ServiceTrait as Service;
use Zend\Framework\Event\EventTrait as Event;

trait EventTrait
{
    /**
     *
     */
    use Event, Service;

    /**
     * @var string
     */
    protected $error = 'error-exception';

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @return mixed
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @param $error
     * @return self
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param Exception $exception
     * @return self
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return Exception
     */
    public function exception()
    {
        return $this->exception;
    }

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
}
