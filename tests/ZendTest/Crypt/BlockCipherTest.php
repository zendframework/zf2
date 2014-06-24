<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt;

use Zend\Crypt\BlockCipher;
use Zend\Crypt\Symmetric\Mcrypt;
use Zend\Crypt\Symmetric\Exception;
use Zend\Crypt\Hmac;
use Zend\Math\Rand;

/**
 * @group      Zend_Crypt
 */
class BlockCipherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockCipher
     */
    protected $blockCipher;
    protected $plaintext;

    public function setUp()
    {
        try {
            $cipher = new Mcrypt(array(
                'algorithm' => 'aes',
                'mode'      => 'cbc',
                'padding'   => 'pkcs7'
            ));
            $this->blockCipher = new BlockCipher($cipher);
        } catch (Exception\RuntimeException $e) {
            $this->markTestSkipped('Mcrypt is not installed, I cannot execute the BlockCipherTest');
        }
        $this->plaintext = file_get_contents(__DIR__ . '/_files/plaintext');
    }

    public function testSetCipher()
    {
        $mcrypt = new Mcrypt();
        $result = $this->blockCipher->setCipher($mcrypt);
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals($mcrypt, $this->blockCipher->getCipher());
    }

    public function testFactory()
    {
        $this->blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'blowfish'));
        $this->assertTrue($this->blockCipher->getCipher() instanceof Mcrypt);
        $this->assertEquals('blowfish', $this->blockCipher->getCipher()->getAlgorithm());
    }

    public function testFactoryEmptyOptions()
    {
        $this->blockCipher = BlockCipher::factory('mcrypt');
        $this->assertTrue($this->blockCipher->getCipher() instanceof Mcrypt);
    }

    public function testSetKey()
    {
        $result = $this->blockCipher->setKey('test');
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals('test', $this->blockCipher->getKey());
    }

    public function testSetSalt()
    {
        $salt = str_repeat('a', $this->blockCipher->getCipher()->getSaltSize() + 2);
        $result = $this->blockCipher->setSalt($salt);
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals(substr($salt, 0,  $this->blockCipher->getCipher()->getSaltSize()),
                            $this->blockCipher->getSalt());
        $this->assertEquals($salt, $this->blockCipher->getOriginalSalt());
    }

    public function testSetAlgorithm()
    {
        $result = $this->blockCipher->setCipherAlgorithm('blowfish');
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals('blowfish', $this->blockCipher->getCipherAlgorithm());
    }

    public function testSetAlgorithmFail()
    {
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'The algorithm unknown is not supported by Zend\Crypt\Symmetric\Mcrypt');
        $result = $this->blockCipher->setCipherAlgorithm('unknown');

    }

    public function testSetHashAlgorithm()
    {
        $result = $this->blockCipher->setHashAlgorithm('sha1');
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals('sha1', $this->blockCipher->getHashAlgorithm());
    }

    public function testSetPbkdf2HashAlgorithm()
    {
        $result = $this->blockCipher->setPbkdf2HashAlgorithm('sha1');
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals('sha1', $this->blockCipher->getPbkdf2HashAlgorithm());
    }

    public function testSetKeyIteration()
    {
        $result = $this->blockCipher->setKeyIteration(1000);
        $this->assertEquals($result, $this->blockCipher);
        $this->assertEquals(1000, $this->blockCipher->getKeyIteration());
    }

    public function testEncryptWithoutData()
    {
        $plaintext = '';
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'The data to encrypt cannot be empty');
        $ciphertext = $this->blockCipher->encrypt($plaintext);
    }

    public function testEncryptErrorKey()
    {
        $plaintext = 'test';
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'No key specified');
        $ciphertext = $this->blockCipher->encrypt($plaintext);
    }

    public function testEncryptDecrypt()
    {
        $this->blockCipher->setKey('test');
        $this->blockCipher->setKeyIteration(1000);
        foreach ($this->blockCipher->getCipherSupportedAlgorithms() as $algo) {
            $this->blockCipher->setCipherAlgorithm($algo);
            $encrypted = $this->blockCipher->encrypt($this->plaintext);
            $this->assertTrue(!empty($encrypted));
            $decrypted = $this->blockCipher->decrypt($encrypted);
            $this->assertEquals($decrypted, $this->plaintext);
        }
    }

    public function testEncryptDecryptUsingBinary()
    {
        $this->blockCipher->setKey('test');
        $this->blockCipher->setKeyIteration(1000);
        $this->blockCipher->setBinaryOutput(true);
        foreach ($this->blockCipher->getCipherSupportedAlgorithms() as $algo) {
            $this->blockCipher->setCipherAlgorithm($algo);
            $encrypted = $this->blockCipher->encrypt($this->plaintext);
            $this->assertTrue(!empty($encrypted));
            $decrypted = $this->blockCipher->decrypt($encrypted);
            $this->assertEquals($decrypted, $this->plaintext);
        }
    }

    public function zeroValuesProvider()
    {
        return array(
            '"0"'   => array(0),
            '"0.0"' => array(0.0),
            '"0"'   => array('0'),
        );
    }

    /**
     * @dataProvider zeroValuesProvider
     */
    public function testEncryptDecryptUsingZero($value)
    {
        $this->blockCipher->setKey('test');
        $this->blockCipher->setKeyIteration(1000);
        foreach ($this->blockCipher->getCipherSupportedAlgorithms() as $algo) {
            $this->blockCipher->setCipherAlgorithm($algo);

            $encrypted = $this->blockCipher->encrypt($value);
            $this->assertTrue(!empty($encrypted));
            $decrypted = $this->blockCipher->decrypt($encrypted);
            $this->assertEquals($value, $decrypted);
        }
    }

    public function testDecryptAuthFail()
    {
        $this->blockCipher->setKey('test');
        $this->blockCipher->setKeyIteration(1000);
        $encrypted = $this->blockCipher->encrypt($this->plaintext);
        $this->assertTrue(!empty($encrypted));
        // tamper the encrypted data
        $encrypted = substr($encrypted, -1);
        $decrypted = $this->blockCipher->decrypt($encrypted);
        $this->assertTrue($decrypted === false);
    }

    public function testEncryptDecryptFile()
    {
        // Test 5 files with a random size bewteen 1 Kb and 5 Mb
        for ($i=1; $i <= 5; $i++) {
            $fileIn  = $this->generateTmpFile(Rand::getInteger(1024, 1048576 * 5), Rand::getBytes(1));
            $fileOut = $fileIn . '.enc';
            $this->blockCipher->setKey('test');

            // encrypt
            $this->assertTrue($this->blockCipher->encryptFile($fileIn, $fileOut));

            // check if the file is compressed
            $this->assertTrue(filesize($fileOut) < fileSize($fileIn));

            $decryptFile = $fileOut . '.dec';
            // decrypt
            $this->assertTrue($this->blockCipher->decryptFile($fileOut, $decryptFile));
            $this->assertEquals(filesize($fileIn), filesize($decryptFile));
            $this->assertEquals(file_get_contents($fileIn), file_get_contents($decryptFile));

            unlink($fileIn);
            unlink($fileOut);
            unlink($decryptFile);
        }
    }

    public function testEncrypDecryptFileNoCOmpression()
    {
        // Test 5 files with a random size between 1 Kb and 5 Mb
        for ($i=1; $i <= 5; $i++) {
            $fileIn  = $this->generateTmpFile(Rand::getInteger(1024, 1048576 * 5), Rand::getBytes(1));
            $fileOut = $fileIn . '.enc';
            $this->blockCipher->setKey('test');

            // encrypt without compression
            $this->assertTrue($this->blockCipher->encryptFile($fileIn, $fileOut, false));

            $paddingSize = $this->blockCipher->getCipher()->getBlockSize();
            $this->assertEquals(filesize($fileOut),
                                filesize($fileIn) +
                                $this->blockCipher->getCipher()->getSaltSize() +
                                Hmac::getOutputSize($this->blockCipher->getHashAlgorithm()) +
                                $paddingSize - filesize($fileIn) % $paddingSize);

            $decryptFile = $fileOut . '.dec';
            // decrypt
            $this->assertTrue($this->blockCipher->decryptFile($fileOut, $decryptFile));
            $this->assertEquals(filesize($fileIn), filesize($decryptFile));
            $this->assertEquals(file_get_contents($fileIn), file_get_contents($decryptFile));

            unlink ($fileIn);
            unlink ($fileOut);
            unlink ($decryptFile);
        }
    }

    public function testDecryptFileNoValidAuthenticate()
    {
        $fileIn  = $this->generateTmpFile(1048576, Rand::getBytes(1));
        $fileOut = $fileIn . '.enc';

        $this->blockCipher->setKey('test');
        $this->assertTrue($this->blockCipher->encryptFile($fileIn, $fileOut, false));

        $fileOut2 = $fileIn . '.dec';
        $this->assertTrue($this->blockCipher->decryptFile($fileOut, $fileOut2, false));
        unlink ($fileOut2);

        // Tampering of the encrypted file
        $ciphertext = file_get_contents($fileOut);
        $ciphertext[0] = chr((ord($ciphertext[0]) + 1) % 256);
        file_put_contents($fileOut, $ciphertext);

        $this->assertFalse($this->blockCipher->decryptFile($fileOut, $fileOut2, false));
        $this->assertFalse(file_exists($fileOut2));

        unlink($fileIn);
        unlink($fileOut);
    }

    public function testEncryptFileWithNoKey()
    {
        $fileIn  = $this->generateTmpFile(1048576, Rand::getBytes(1));
        $fileOut = $fileIn . '.enc';

        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'No key specified');
        $this->blockCipher->encryptFile($fileIn, $fileOut);

        unlink($fileIn);
    }

    public function testDecryptFileWithNoKey()
    {
        $fileIn  = $this->generateTmpFile(1048576, Rand::getBytes(1));
        $fileOut = $fileIn . '.enc';

        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    'No key specified');
        $this->blockCipher->decryptFile($fileIn, $fileOut);

        unlink($fileIn);
    }

    public function testEncryptFileInvalidInputFile()
    {
        $randomFile = uniqid('Invalid_File');
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    "I cannot open the $randomFile file");
        $this->blockCipher->setKey('test');
        $this->blockCipher->encryptFile($randomFile, '');
    }

    public function testDecryptFileInvalidInputFile()
    {
        $randomFile = uniqid('Invalid_File');
        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    "I cannot open the $randomFile file");
        $this->blockCipher->setKey('test');
        $this->blockCipher->decryptFile($randomFile, '');

    }

    public function testEncryptFileInvalidOutputFile()
    {
        $fileIn  = $this->generateTmpFile(1024);
        $fileOut = $this->generateTmpFile(1024);

        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    "The file $fileOut already exists");
        $this->blockCipher->setKey('test');
        $this->blockCipher->encryptFile($fileIn, $fileOut);

        unlink($fileIn);
        unlink($fileOut);
    }

    public function testDecryptFileInvalidOutputFile()
    {
        $fileIn  = $this->generateTmpFile(1024);
        $fileOut = $this->generateTmpFile(1024);

        $this->setExpectedException('Zend\Crypt\Exception\InvalidArgumentException',
                                    "The file $fileOut already exists");
        $this->blockCipher->setKey('test');
        $this->blockCipher->decryptFile($fileIn, $fileOut);

        unlink($fileIn);
        unlink($fileOut);
    }

    /**
     * Generate a temporary file with a selected size
     *
     * @param  string $size
     * @param  string $content
     * @return string
     */
    protected function generateTmpFile($size, $content = 'A')
    {
        $fileName = sys_get_temp_dir() . '/' . uniqid('ZF2_BlockCipher_test');
        $num = $size / strlen($content) + 1;
        $content  = str_repeat('A', $size / strlen($content) + 1);
        file_put_contents($fileName, substr($content, 0, $size));

        return $fileName;
    }
}
