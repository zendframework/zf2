<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Header;

/**
 * This test is primarily to test that AbstractAddressList headers perform
 * header folding and MIME encoding properly.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class SenderTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $header = new Header\Sender();
        $header->setAddress('foo@bar.com', 'Foobar');

        $this->assertSame('Sender: Foobar <foo@bar.com>', $header->toString());
    }

    public function testEmptyWhenNoAddressSpecified()
    {
        $header = new Header\Sender();

        $this->assertEmpty($header->toString());
    }
}
