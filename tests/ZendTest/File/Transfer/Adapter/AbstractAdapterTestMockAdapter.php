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

/**
 * Test class for Zend\File\Transfer\Adapter\AbstractAdapter
 *
 * @group      Zend_File
 */
class AbstractAdapterTestMockAdapter extends Adapter\AbstractAdapter
{
    public $received = false;

    public $tmpDir;

    public function __construct()
    {
        $testfile = __DIR__ . '/_files/test.txt';
        $this->files = [
            'foo' => [
                'name'      => 'foo.jpg',
                'type'      => 'image/jpeg',
                'size'      => 126976,
                'tmp_name'  => '/tmp/489127ba5c89c',
                'options'   => ['ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true],
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ],
            'bar' => [
                'name'     => 'bar.png',
                'type'     => 'image/png',
                'size'     => 91136,
                'tmp_name' => '/tmp/489128284b51f',
                'options'  => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ],
            'baz' => [
                'name'     => 'baz.text',
                'type'     => 'text/plain',
                'size'     => 1172,
                'tmp_name' => $testfile,
                'options'  => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ],
            'file_0_' => [
                'name'      => 'foo.jpg',
                'type'      => 'image/jpeg',
                'size'      => 126976,
                'tmp_name'  => '/tmp/489127ba5c89c',
                'options'   => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ],
            'file_1_' => [
                'name'     => 'baz.text',
                'type'     => 'text/plain',
                'size'     => 1172,
                'tmp_name' => $testfile,
                'options'  => ['ignoreNoFile' => false, 'useByteString' => true],
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ],
            'file' => [
                'name'      => 'foo.jpg',
                'multifiles' => [0 => 'file_0_', 1 => 'file_1_']
            ],
        ];
    }

    public function send($options = null)
    {
        return;
    }

    public function receive($options = null)
    {
        $this->received = true;
        return;
    }

    public function isSent($file = null)
    {
        return false;
    }

    public function isReceived($file = null)
    {
        return $this->received;
    }

    public function isUploaded($files = null)
    {
        return true;
    }

    public function isFiltered($files = null)
    {
        return true;
    }

    public static function getProgress()
    {
        return;
    }

    public function getTmpDir()
    {
        $this->tmpDir = parent::getTmpDir();
    }

    public function isPathWriteable($path)
    {
        return parent::isPathWriteable($path);
    }

    public function addInvalidFile()
    {
        $this->files += [
            'test' => [
                'name'      => 'test.txt',
                'type'      => 'image/jpeg',
                'size'      => 0,
                'tmp_name'  => '',
                'options'   => ['ignoreNoFile' => true, 'useByteString' => true],
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ]
        ];
    }
}
