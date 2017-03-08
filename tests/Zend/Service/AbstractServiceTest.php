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
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service;

use Zend\Service\AbstractService;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 */
class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefaultHttpClientThrowException()
    {
        $this->setExpectedException('InvalidArgumentException');
        AbstractService::setDefaultHttpClient(new \stdClass);
    }

    public function testGetDefaultHttpClientThrowException()
    {
        try {
            AbstractService::setDefaultHttpClient('foo');
            AbstractService::getDefaultHttpClient();
        } catch (\InvalidArgumentException $e) {
        }

        try {
            AbstractService::setDefaultHttpClient(get_class($this));
            AbstractService::getDefaultHttpClient();
        } catch (\InvalidArgumentException $e) {
            return;
        }
        $this->fail('getDefaultHttpClient should throw Exception when not HttpClient instance');
    }
}

