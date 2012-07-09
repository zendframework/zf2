<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 */

namespace Zend\Search\Lucene\Search\Exception;

use Zend\Search\Lucene\Exception;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 *
 * Special exception type, which may be used to intercept wrong user input
 */
class QueryParserException
    extends Exception\UnexpectedValueException
    implements ExceptionInterface
{}

