<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File;

/**
 * @group      Zend_Validator
 */
class ExistsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $baseDir  = dirname($testFile);
        $baseName = basename($testFile);
        $fileUpload = [
            'tmp_name' => $testFile, 'name' => basename($testFile),
            'size' => 200, 'error' => 0, 'type' => 'text'
        ];
        return [
            //    Options, isValid Param, Expected value
            [dirname($baseDir), $baseName,   false],
            [$baseDir,          $baseName,   true],
            [$baseDir,          $testFile,   true],
            [dirname($baseDir), $fileUpload, false],
            [$baseDir,          $fileUpload, true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testBasic($options, $isValidParam, $expected)
    {
        $validator = new File\Exists($options);
        $this->assertEquals($expected, $validator->isValid($isValidParam));
    }

    /**
     * Ensures that the validator follows expected behavior for legacy Zend\Transfer API
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testLegacy($options, $isValidParam, $expected)
    {
        if (is_array($isValidParam)) {
            $validator = new File\Exists($options);
            $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
        }
    }

    /**
     * Ensures that getDirectory() returns expected value
     *
     * @return void
     */
    public function testGetDirectory()
    {
        $validator = new File\Exists('C:/temp');
        $this->assertEquals('C:/temp', $validator->getDirectory());

        $validator = new File\Exists(['temp', 'dir', 'jpg']);
        $this->assertEquals('temp,dir,jpg', $validator->getDirectory());

        $validator = new File\Exists(['temp', 'dir', 'jpg']);
        $this->assertEquals(['temp', 'dir', 'jpg'], $validator->getDirectory(true));
    }

    /**
     * Ensures that setDirectory() returns expected value
     *
     * @return void
     */
    public function testSetDirectory()
    {
        $validator = new File\Exists('temp');
        $validator->setDirectory('gif');
        $this->assertEquals('gif', $validator->getDirectory());
        $this->assertEquals(['gif'], $validator->getDirectory(true));

        $validator->setDirectory('jpg, temp');
        $this->assertEquals('jpg,temp', $validator->getDirectory());
        $this->assertEquals(['jpg', 'temp'], $validator->getDirectory(true));

        $validator->setDirectory(['zip', 'ti']);
        $this->assertEquals('zip,ti', $validator->getDirectory());
        $this->assertEquals(['zip', 'ti'], $validator->getDirectory(true));
    }

    /**
     * Ensures that addDirectory() returns expected value
     *
     * @return void
     */
    public function testAddDirectory()
    {
        $validator = new File\Exists('temp');
        $validator->addDirectory('gif');
        $this->assertEquals('temp,gif', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif'], $validator->getDirectory(true));

        $validator->addDirectory('jpg, to');
        $this->assertEquals('temp,gif,jpg,to', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif', 'jpg', 'to'], $validator->getDirectory(true));

        $validator->addDirectory(['zip', 'ti']);
        $this->assertEquals('temp,gif,jpg,to,zip,ti', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getDirectory(true));

        $validator->addDirectory('');
        $this->assertEquals('temp,gif,jpg,to,zip,ti', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getDirectory(true));
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Exists(__DIR__);
        $this->assertFalse($validator->isValid('nofile.mo'));
        $this->assertArrayHasKey('fileExistsDoesNotExist', $validator->getMessages());
        $this->assertContains("does not exist", current($validator->getMessages()));
    }

    public function testEmptyFileArrayShouldReturnFalse()
    {
        $validator = new File\Exists();

        $this->assertFalse($validator->isValid(''));
        $this->assertArrayHasKey(File\Exists::DOES_NOT_EXIST, $validator->getMessages());

        $filesArray = [
            'name'      => '',
            'size'      => 0,
            'tmp_name'  => '',
            'error'     => UPLOAD_ERR_NO_FILE,
            'type'      => '',
        ];

        $this->assertFalse($validator->isValid($filesArray));
        $this->assertArrayHasKey(File\Exists::DOES_NOT_EXIST, $validator->getMessages());
    }
}
