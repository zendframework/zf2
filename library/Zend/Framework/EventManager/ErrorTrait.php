<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

trait ErrorTrait
{
    /**
     * @var mixed
     */
    protected $error;

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
}
