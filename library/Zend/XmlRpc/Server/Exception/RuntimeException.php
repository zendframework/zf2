<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace Zend\XmlRpc\Server\Exception;

use Zend\XmlRpc\Exception;

/**
 * @package    Zend_XmlRpc
 * @subpackage Server_Exception
 */
class RuntimeException extends Exception\RuntimeException implements ExceptionInterface
{
}
