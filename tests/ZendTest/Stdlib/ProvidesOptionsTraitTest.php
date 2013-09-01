<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\SplPriorityQueue;
use ZendTest\Stdlib\TestAsset\TestOptionsTrait;

/**
 * @group Zend_Stdlib
 */
class ProvidesOptionsTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSetValidOptions()
    {
        $options = new TestOptionsTrait();
        $options->setOptions(array(
            'street'     => '1 Rue Favier',
            'first_name' => 'Julien'
        ));

        $this->assertEquals('1 Rue Favier', $options->getStreet());
        $this->assertEquals('Julien', $options->getFirstName());
    }

    public function testThrowExceptionForInvalidOption()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException');

        $options = new TestOptionsTrait();
        $options->setOptions(array('invalid_option' => 'bar'));
    }
}
