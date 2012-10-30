<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Storage;

use Zend\Authentication\Storage;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @group      Zend_Auth
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->session = new Storage\Session('Session');
    }

    public function testCanCallMethod()
    {
        $this->assertEquals('Session', $this->session->getName());
    }
}
