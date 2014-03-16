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
use Zend\Uri\Http as Uri;

trait EventTrait
{
    /**
     *
     */
    use Event;

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var int
     */
    protected $pathOffset;

    /**
     * @return int|null
     */
    public function pathOffset()
    {
        return $this->pathOffset;
    }

    /**
     * @return Uri
     */
    public function uri()
    {
        return $this->uri;
    }
}
