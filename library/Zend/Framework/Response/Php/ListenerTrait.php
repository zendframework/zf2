<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Php;

use Zend\Framework\Response\ListenerTrait as Listener;
use Zend\Framework\Response\SendContentTrait as SendContent;
use Zend\Framework\Response\SendHeadersTrait as SendHeaders;

trait ListenerTrait
{
    /**
     *
     */
    use Listener, SendContent, SendHeaders;
}
