<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\PublicKey\Rsa\Exception;

use Zend\Crypt\Exception;

/**
 * Runtime argument exception
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage PublicKey
 */
class RuntimeException
    extends Exception\RuntimeException
    implements ExceptionInterface
{}
