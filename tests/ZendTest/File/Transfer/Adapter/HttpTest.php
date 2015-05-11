<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\File\Transfer\Adapter;

use Zend\File\Transfer\Adapter;
use Zend\File\Transfer\Exception\RuntimeException;
use Zend\ProgressBar\Adapter as AdapterProgressBar;
use Zend\Validator\File as FileValidator;

/**
 * Test class for Zend\File\Transfer\Adapter\Http
 *
 * @group      Zend_File
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $_FILES = [
            'txt' => [
                'name' => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test.txt',
                'type' => 'plain/text',
                'size' => 8,
                'tmp_name' => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test.txt',
                'error' => 0]];
        $this->adapter = new HttpTestMockAdapter();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testEmptyAdapter()
    {
        $files = $this->adapter->getFileName();
        $this->assertContains('test.txt', $files);
    }

    public function testAutoSetUploadValidator()
    {
        $validators = [
            new FileValidator\Count(1),
            new FileValidator\Extension('jpg'),
        ];
        $this->adapter->setValidators($validators);
        $test = $this->adapter->getValidator('Upload');
        $this->assertInstanceOf('Zend\Validator\File\Upload', $test);
    }

    public function testSendingFiles()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\BadMethodCallException', 'not implemented');
        $this->adapter->send();
    }

    public function testFileIsSent()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\BadMethodCallException', 'not implemented');
        $this->adapter->isSent();
    }

    public function testFileIsUploaded()
    {
        $this->assertTrue($this->adapter->isUploaded());
    }

    public function testFileIsNotUploaded()
    {
        $this->assertFalse($this->adapter->isUploaded('unknownFile'));
    }

    public function testFileIsNotFiltered()
    {
        $this->assertFalse($this->adapter->isFiltered('unknownFile'));
        $this->assertFalse($this->adapter->isFiltered());
    }

    public function testFileIsNotReceived()
    {
        $this->assertFalse($this->adapter->isReceived('unknownFile'));
        $this->assertFalse($this->adapter->isReceived());
    }

    public function testReceiveUnknownFile()
    {
        try {
            $this->assertFalse($this->adapter->receive('unknownFile'));
        } catch (RuntimeException $e) {
            $this->assertContains('not find', $e->getMessage());
        }
    }

    public function testReceiveValidatedFile()
    {
        $_FILES = [
            'txt' => [
                'name' => 'unknown.txt',
                'type' => 'plain/text',
                'size' => 8,
                'tmp_name' => 'unknown.txt',
                'error' => 0]];
        $adapter = new HttpTestMockAdapter();
        $this->assertFalse($adapter->receive());
    }

    public function testReceiveIgnoredFile()
    {
        $this->adapter->setOptions(['ignoreNoFile' => true]);
        $this->assertTrue($this->adapter->receive());
    }

    public function testReceiveWithRenameFilter()
    {
        $this->adapter->addFilter('Rename', ['target' => '/testdir']);
        $this->adapter->setOptions(['ignoreNoFile' => true]);
        $this->assertTrue($this->adapter->receive());
    }

    public function testReceiveWithRenameFilterButWithoutDirectory()
    {
        $this->adapter->setDestination(__DIR__);
        $this->adapter->addFilter('Rename', ['overwrite' => false]);
        $this->adapter->setOptions(['ignoreNoFile' => true]);
        $this->assertTrue($this->adapter->receive());
    }

    public function testMultiFiles()
    {
        $_FILES = [
            'txt' => [
                'name' => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test.txt',
                'type' => 'plain/text',
                'size' => 8,
                'tmp_name' => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test.txt',
                'error' => 0],
            'exe' => [
                'name' => [
                    0 => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'file1.txt',
                    1 => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'file2.txt'],
                'type' => [
                    0 => 'plain/text',
                    1 => 'plain/text'],
                'size' => [
                    0 => 8,
                    1 => 8],
                'tmp_name' => [
                    0 => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'file1.txt',
                    1 => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'file2.txt'],
                'error' => [
                    0 => 0,
                    1 => 0]]];
        $adapter = new HttpTestMockAdapter();
        $adapter->setOptions(['ignoreNoFile' => true]);
        $this->assertTrue($adapter->receive('exe'));
        $this->assertEquals(
            ['exe_0_' => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'file1.txt',
                  'exe_1_' => __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'file2.txt'],
            $adapter->getFileName('exe', false));
    }

    public function testNoUploadInProgress()
    {
        if (!Adapter\Http::isApcAvailable() && !Adapter\Http::isUploadProgressAvailable()) {
            $this->markTestSkipped('Whether APC nor UploadExtension available');
        }

        $status = HttpTestMockAdapter::getProgress();
        $this->assertContains('No upload in progress', $status);
    }

    public function testUploadProgressFailure()
    {
        if (!Adapter\Http::isApcAvailable() && !Adapter\Http::isUploadProgressAvailable()) {
            $this->markTestSkipped('Whether APC nor UploadExtension available');
        }

        $_GET['progress_key'] = 'mykey';
        $status = HttpTestMockAdapter::getProgress();
        $this->assertEquals([
            'total'   => 100,
            'current' => 100,
            'rate'    => 10,
            'id'      => 'mykey',
            'done'    => false,
            'message' => '100B - 100B'
            ], $status);

        $this->adapter->switchApcToUP();
        $status = HttpTestMockAdapter::getProgress($status);
        $this->assertEquals([
            'total'          => 100,
            'bytes_total'    => 100,
            'current'        => 100,
            'bytes_uploaded' => 100,
            'rate'           => 10,
            'speed_average'  => 10,
            'cancel_upload'  => true,
            'message'        => 'The upload has been canceled',
            'done'           => true,
            'id'             => 'mykey'
            ], $status);
    }

    public function testUploadProgressAdapter()
    {
        if (!Adapter\Http::isApcAvailable() && !Adapter\Http::isUploadProgressAvailable()) {
            $this->markTestSkipped('Whether APC nor UploadExtension available');
        }

        $_GET['progress_key'] = 'mykey';
        $adapter = new AdapterProgressBar\Console();
        $status = ['progress' => $adapter, 'session' => 'upload'];
        $status = HttpTestMockAdapter::getProgress($status);
        $this->assertArrayHasKey('total', $status);
        $this->assertArrayHasKey('current', $status);
        $this->assertArrayHasKey('rate', $status);
        $this->assertArrayHasKey('id', $status);
        $this->assertArrayHasKey('message', $status);
        $this->assertArrayHasKey('progress', $status);
        $this->assertInstanceOf('Zend\ProgressBar\ProgressBar', $status['progress']);

        $this->adapter->switchApcToUP();
        $status = HttpTestMockAdapter::getProgress($status);
        $this->assertArrayHasKey('total', $status);
        $this->assertArrayHasKey('current', $status);
        $this->assertArrayHasKey('rate', $status);
        $this->assertArrayHasKey('id', $status);
        $this->assertArrayHasKey('message', $status);
        $this->assertArrayHasKey('progress', $status);
        $this->assertInstanceOf('Zend\ProgressBar\ProgressBar', $status['progress']);
    }

    public function testValidationOfPhpExtendsFormError()
    {
        $_SERVER['CONTENT_LENGTH'] = 10;

        $_FILES = [];
        $adapter = new HttpTestMockAdapter();
        $this->assertFalse($adapter->isValidParent());
        $this->assertContains('exceeds the defined ini size', current($adapter->getMessages()));
    }
}
