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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Tool\Framework\Client;
use Zend\Tool\Framework\Client\Storage\Directory,
    Zend\Tool\Framework\Client\Storage\Exception\UnexpectedValueException;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Client
 * @group Zend_Tool_Framework_Client_Storage
 */
class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    public function testNonExistsDirectory()
    {
        try {
            $directory = new Directory(__DIR__.DIRECTORY_SEPARATOR.'nonexists');
            $this->fail('RuntimeException was expected but not thrown');
        } catch (UnexpectedValueException $ue) {
        } 
    }
}
