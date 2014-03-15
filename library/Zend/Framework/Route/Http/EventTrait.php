<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Event\EventTrait as Event;
use Zend\Stdlib\RequestInterface as Request;

trait EventTrait
{
    /**
     *
     */
    use Event;

    /**
     * @var Request;
     */
    protected $request;

    /**
     * @var int
     */
    protected $baseUrlLength;

    /**
     * @var int
     */
    protected $pathLength;

    /**
     * @return null|int
     */
    public function baseUrlLength()
    {
        return $this->baseUrlLength;
    }

    /**
     * @return int|null
     */
    public function pathLength()
    {
        return $this->pathLength;
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }
}
