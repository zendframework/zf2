<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace ZendTest\Config\Writer;

use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;
use ZendTest\Config\Writer\TestAssets\PhpReader;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @group      Zend_Config
 */
class PhpArrayTest extends AbstractWriterTestCase
{
    protected $_tempName;

    public function setUp()
    {
        $this->writer = new PhpArray();
        $this->reader = new PhpReader();
    }

    /**
     * @group ZF-8234
     */
    public function testRender()
    {
        $array = array(
            'test' => 'foo',
            'with 2 spaces' => 'foo  bar',
            'with 4 spaces' => 'foo    bar',
            'bar' => array(
                0 => 'baz',
                1 => 'foo',
                'sub-sub' => array(
                    'foo' => 'bar'
                )
            )
        );
        $config = new Config($array);

        // build string line by line as we are trailing-whitespace sensitive.
        $expected = "<?php\n";
        $expected .= "return array (\n";
        $expected .= "    'test' => 'foo',\n";
        $expected .= "    'with 2 spaces' => 'foo  bar',\n";
        $expected .= "    'with 4 spaces' => 'foo    bar',\n";
        $expected .= "    'bar' => array (\n";
        $expected .= "        0 => 'baz',\n";
        $expected .= "        1 => 'foo',\n";
        $expected .= "        'sub-sub' => array (\n";
        $expected .= "            'foo' => 'bar',\n";
        $expected .= "        ),\n";
        $expected .= "    ),\n";
        $expected .= ");\n";

        $arrayString = $this->writer->toString($array);
        $configString = $this->writer->toString($config);

        $this->assertEquals($expected, $configString);
        $this->assertEquals($expected, $arrayString);
    }
}
