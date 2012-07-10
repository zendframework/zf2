<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File;

/**
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class ImageSizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(array('min_width' => 0,   'min_height' => 10,  'max_width' => 1000, 'max_height' => 2000), true),
            array(array('min_width' => 0,   'min_height' => 0,   'max_width' => 200,  'max_height' => 200), true),
            array(array('min_width' => 150, 'min_height' => 150, 'max_width' => 200,  'max_height' => 200), false),
            array(array('min_width' => 80,  'min_height' => 0,   'max_width' => 80,   'max_height' => 200), true),
            array(array('min_width' => 0,   'min_height' => 0,   'max_width' => 60,   'max_height' => 200), false),
            array(array('min_width' => 90,  'min_height' => 0,   'max_width' => 200,  'max_height' => 200), false),
            array(array('min_width' => 0,   'min_height' => 0,   'max_width' => 200,  'max_height' => 80), false),
            array(array('min_width' => 0,   'min_height' => 110, 'max_width' => 200,  'max_height' => 140), false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new File\ImageSize($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new File\ImageSize(array('min_width' => 0, 'min_height' => 10, 'max_width' => 1000, 'max_height' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg'));
        $failures = $validator->getMessages();
        $this->assertContains('is not readable', $failures['fileImageSizeNotReadable']);

        $file['name'] = 'TestName';
        $validator = new File\ImageSize(array('min_width' => 0, 'min_height' => 10, 'max_width' => 1000, 'max_height' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg', $file));
        $failures = $validator->getMessages();
        $this->assertContains('TestName', $failures['fileImageSizeNotReadable']);

        $validator = new File\ImageSize(array('min_width' => 0, 'min_height' => 10, 'max_width' => 1000, 'max_height' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/badpicture.jpg'));
        $failures = $validator->getMessages();
        $this->assertContains('could not be detected', $failures['fileImageSizeNotDetected']);
    }

    /**
     * Ensures that getImageMin() returns expected value
     *
     * @return void
     */
    public function testGetImageMin()
    {
        $validator = new File\ImageSize(array('min_width' => 1, 'min_height' => 10, 'max_width' => 100, 'max_height' => 1000));
        $this->assertEquals(array('min_width' => 1, 'min_height' => 10), $validator->getImageMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\ImageSize(array('min_width' => 1000, 'min_height' => 100, 'max_width' => 10, 'max_height' => 1));
    }

    /**
     * Ensures that setImageMin() returns expected value
     *
     * @return void
     */
    public function testSetImageMin()
    {
        $validator = new File\ImageSize(array('min_width' => 100, 'min_height' => 1000, 'max_width' => 10000, 'max_height' => 100000));
        $validator->setImageMin(array('min_width' => 10, 'min_height' => 10));
        $this->assertEquals(array('min_width' => 10, 'min_height' => 10), $validator->getImageMin());

        $validator->setImageMin(array('min_width' => 9, 'min_height' => 100));
        $this->assertEquals(array('min_width' => 9, 'min_height' => 100), $validator->getImageMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageMin(array('min_width' => 20000, 'min_height' => 20000));
    }

    /**
     * Ensures that getImageMax() returns expected value
     *
     * @return void
     */
    public function testGetImageMax()
    {
        $validator = new File\ImageSize(array('min_width' => 10, 'min_height' => 100, 'max_width' => 1000, 'max_height' => 10000));
        $this->assertEquals(array('max_width' => 1000, 'max_height' => 10000), $validator->getImageMax());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\ImageSize(array('min_width' => 10000, 'min_height' => 1000, 'max_width' => 100, 'max_height' => 10));
    }

    /**
     * Ensures that setImageMax() returns expected value
     *
     * @return void
     */
    public function testSetImageMax()
    {
        $validator = new File\ImageSize(array('min_width' => 10, 'min_height' => 100, 'max_width' => 1000, 'max_height' => 10000));
        $validator->setImageMax(array('max_width' => 100, 'max_height' => 100));
        $this->assertEquals(array('max_width' => 100, 'max_height' => 100), $validator->getImageMax());

        $validator->setImageMax(array('max_width' => 110, 'max_height' => 1000));
        $this->assertEquals(array('max_width' => 110, 'max_height' => 1000), $validator->getImageMax());

        $validator->setImageMax(array('max_height' => 1100));
        $this->assertEquals(array('max_width' => 110, 'max_height' => 1100), $validator->getImageMax());

        $validator->setImageMax(array('max_width' => 120));
        $this->assertEquals(array('max_width' => 120, 'max_height' => 1100), $validator->getImageMax());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator->setImageMax(array('max_width' => 10000, 'max_height' => 1));
    }

    /**
     * Ensures that getImageWidth() returns expected value
     *
     * @return void
     */
    public function testGetImageWidth()
    {
        $validator = new File\ImageSize(array('min_width' => 1, 'min_height' => 10, 'max_width' => 100, 'max_height' => 1000));
        $this->assertEquals(array('min_width' => 1, 'max_width' => 100), $validator->getImageWidth());
    }

    /**
     * Ensures that setImageWidth() returns expected value
     *
     * @return void
     */
    public function testSetImageWidth()
    {
        $validator = new File\ImageSize(array('min_width' => 100, 'min_height' => 1000, 'max_width' => 10000, 'max_height' => 100000));
        $validator->setImageWidth(array('min_width' => 2000, 'max_width' => 2200));
        $this->assertEquals(array('min_width' => 2000, 'max_width' => 2200), $validator->getImageWidth());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageWidth(array('min_width' => 20000, 'max_width' => 200));
    }

    /**
     * Ensures that getImageHeight() returns expected value
     *
     * @return void
     */
    public function testGetImageHeight()
    {
        $validator = new File\ImageSize(array('min_width' => 1, 'min_height' => 10, 'max_width' => 100, 'max_height' => 1000));
        $this->assertEquals(array('min_height' => 10, 'max_height' => 1000), $validator->getImageHeight());
    }

    /**
     * Ensures that setImageHeight() returns expected value
     *
     * @return void
     */
    public function testSetImageHeight()
    {
        $validator = new File\ImageSize(array('min_width' => 100, 'min_height' => 1000, 'max_width' => 10000, 'max_height' => 100000));
        $validator->setImageHeight(array('min_height' => 2000, 'max_height' => 2200));
        $this->assertEquals(array('min_height' => 2000, 'max_height' => 2200), $validator->getImageHeight());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageHeight(array('min_height' => 20000, 'max_height' => 200));
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\ImageSize(array('min_width' => 100, 'min_height' => 1000, 'max_width' => 10000, 'max_height' => 100000));
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileImageSizeNotReadable', $validator->getMessages()));
        $this->assertContains("'nofile.mo'", current($validator->getMessages()));
    }
}
