<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Json\Server\Exception;

use Zend\Json\Exception;

/**
 * Thrown by Zend\Json\Server\Client when an JSON-RPC fault response is returned.
 */
class ErrorException extends Exception\BadMethodCallException implements
    ExceptionInterface
{}
