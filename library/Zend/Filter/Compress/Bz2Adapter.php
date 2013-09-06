<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Compress;

use Zend\Filter\Exception;

/**
 * Compression adapter for Bz2
 */
class Bz2Adapter extends AbstractCompressionAdapter
{
    /**
     * @var int
     */
    protected $blockSize = 4;

    /**
     * @var string
     */
    protected $archive;

    /**
     * Class constructor
     *
     * @param null|array|\Traversable $options (Optional) Options to set
     * @throws Exception\ExtensionNotLoadedException if bz2 extension not loaded
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('bz2')) {
            throw new Exception\ExtensionNotLoadedException('This filter needs the bz2 extension');
        }

        parent::__construct($options);
    }

    /**
     * Returns the set block size
     *
     * @return int
     */
    public function getBlockSize()
    {
        return $this->blockSize;
    }

    /**
     * Sets a new blocksize
     *
     * @param  int $blockSize
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setBlockSize($blockSize)
    {
        if (($blockSize < 0) || ($blockSize > 9)) {
            throw new Exception\InvalidArgumentException('Block size must be between 0 and 9');
        }

        $this->blockSize = (int) $blockSize;
    }

    /**
     * Returns the set archive
     *
     * @return string
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Sets the archive to use for de-/compression
     *
     * @param  string $archive Archive to use
     * @return void
     */
    public function setArchive($archive)
    {
        $this->archive = (string) $archive;
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        $archive = $this->getArchive();
        if (!empty($archive)) {
            $file = bzopen($archive, 'w');
            if (!$file) {
                throw new Exception\RuntimeException("Error opening the archive $archive");
            }

            bzwrite($file, $content);
            bzclose($file);
            $compressed = true;
        } else {
            $compressed = bzcompress($content, $this->getBlocksize());
        }

        if (is_int($compressed)) {
            throw new Exception\RuntimeException('Error during compression');
        }

        return $compressed;
    }

    /**
     * {@inheritDoc}
     */
    public function decompress($content)
    {
        $archive = $this->getArchive();
        if (file_exists($content)) {
            $archive = $content;
        }

        if (file_exists($archive)) {
            $file = bzopen($archive, 'r');
            if (!$file) {
                throw new Exception\RuntimeException("Error opening the archive $content");
            }

            $compressed = bzread($file);
            bzclose($file);
        } else {
            $compressed = bzdecompress($content);
        }

        if (is_int($compressed)) {
            throw new Exception\RuntimeException('Error during decompression');
        }

        return $compressed;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return 'Bz2';
    }
}
