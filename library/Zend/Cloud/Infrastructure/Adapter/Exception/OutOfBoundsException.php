<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\Infrastructure\Adapter\Exception;

use Zend\Cloud\Infrastructure\Exception;

/**
 * @package    Zend_Cloud
 * @subpackage Infrastructure_Adapter
 */
class OutOfBoundsException extends Exception\OutOfBoundsException implements
    ExceptionInterface
{}
