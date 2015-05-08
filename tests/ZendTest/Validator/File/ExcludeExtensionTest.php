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
class ExcludeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile   = __DIR__ . '/_files/testsize.mo';
        $pictureTests = [
            //    Options, isValid Param, Expected value, Expected message
            ['mo',                       $testFile, false,  'fileExcludeExtensionFalse'],
            ['gif',                      $testFile, true, ''],
            [['mo'],                $testFile, false,  'fileExcludeExtensionFalse'],
            [['gif'],               $testFile, true, ''],
            [['gif', 'mo', 'pict'], $testFile, false,  'fileExcludeExtensionFalse'],
            [['gif', 'gz', 'hint'], $testFile, true, ''],
        ];

        $testFile   = __DIR__ . '/_files/nofile.mo';
        $noFileTests = [
            //    Options, isValid Param, Expected value, message
            ['mo', $testFile, false, 'fileExcludeExtensionNotFound'],
        ];

        // Dupe data in File Upload format
        $testData = array_merge($pictureTests, $noFileTests);
        foreach ($testData as $data) {
            $fileUpload = [
                'tmp_name' => $data[1], 'name' => basename($data[1]),
                'size' => 200, 'error' => 0, 'type' => 'text'
            ];
            $testData[] = [$data[0], $fileUpload, $data[2], $data[3]];
        }
        return $testData;
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testBasic($options, $isValidParam, $expected, $messageKey)
    {
        $validator = new File\ExcludeExtension($options);
        $this->assertEquals($expected, $validator->isValid($isValidParam));
        if (!$expected) {
            $this->assertArrayHasKey($messageKey, $validator->getMessages());
        }
    }

    /**
     * Ensures that the validator follows expected behavior for legacy Zend\Transfer API
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testLegacy($options, $isValidParam, $expected, $messageKey)
    {
        if (is_array($isValidParam)) {
            $validator = new File\ExcludeExtension($options);
            $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
            if (!$expected) {
                $this->assertArrayHasKey($messageKey, $validator->getMessages());
            }
        }
    }

    public function testCaseTesting()
    {
        $files = [
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        ];
        $validator = new File\ExcludeExtension(['MO', 'case' => true]);
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));

        $validator = new File\ExcludeExtension(['MO', 'case' => false]);
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
    }

    /**
     * Ensures that getExtension() returns expected value
     *
     * @return void
     */
    public function testGetExtension()
    {
        $validator = new File\ExcludeExtension('mo');
        $this->assertEquals(['mo'], $validator->getExtension());

        $validator = new File\ExcludeExtension(['mo', 'gif', 'jpg']);
        $this->assertEquals(['mo', 'gif', 'jpg'], $validator->getExtension());
    }

    /**
     * Ensures that setExtension() returns expected value
     *
     * @return void
     */
    public function testSetExtension()
    {
        $validator = new File\ExcludeExtension('mo');
        $validator->setExtension('gif');
        $this->assertEquals(['gif'], $validator->getExtension());

        $validator->setExtension('jpg, mo');
        $this->assertEquals(['jpg', 'mo'], $validator->getExtension());

        $validator->setExtension(['zip', 'ti']);
        $this->assertEquals(['zip', 'ti'], $validator->getExtension());
    }

    /**
     * Ensures that addExtension() returns expected value
     *
     * @return void
     */
    public function testAddExtension()
    {
        $validator = new File\ExcludeExtension('mo');
        $validator->addExtension('gif');
        $this->assertEquals(['mo', 'gif'], $validator->getExtension());

        $validator->addExtension('jpg, to');
        $this->assertEquals(['mo', 'gif', 'jpg', 'to'], $validator->getExtension());

        $validator->addExtension(['zip', 'ti']);
        $this->assertEquals(['mo', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getExtension());

        $validator->addExtension('');
        $this->assertEquals(['mo', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getExtension());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\ExcludeExtension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertArrayHasKey('fileExcludeExtensionNotFound', $validator->getMessages());
        $this->assertContains("does not exist", current($validator->getMessages()));
    }

    public function testEmptyFileShouldReturnFalseAndDisplayNotFoundMessage()
    {
        $validator = new File\ExcludeExtension('12345');

        $this->assertFalse($validator->isValid(''));
        $this->assertArrayHasKey(File\ExcludeExtension::NOT_FOUND, $validator->getMessages());

        $filesArray = [
            'name'      => '',
            'size'      => 0,
            'tmp_name'  => '',
            'error'     => UPLOAD_ERR_NO_FILE,
            'type'      => '',
        ];

        $this->assertFalse($validator->isValid($filesArray));
        $this->assertArrayHasKey(File\ExcludeExtension::NOT_FOUND, $validator->getMessages());
    }
}
