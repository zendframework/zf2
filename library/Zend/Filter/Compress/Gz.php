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
 * Compression adapter for Gzip (ZLib)
 */
class Gz extends AbstractCompressionAdapter
{
    /**
     * Compression mode constants
     */
    const COMPRESSION_MODE_COMPRESS = 'compress';
    const COMPRESSION_MODE_DEFLATE  = 'deflate';

    /**
     * Compression level (0-9)
     *
     * @var int
     */
    protected $level = 9;

    /**
     * Compression mode (can be compress or deflate)
     *
     * @var string
     */
    protected $compressionMode = self::COMPRESSION_MODE_COMPRESS;

    /**
     * @var string
     */
    protected $archive;

    /**
     * Class constructor
     *
     * @param null|array|\Traversable $options (Optional) Options to set
     * @throws Exception\ExtensionNotLoadedException if zlib extension not loaded
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('zlib')) {
            throw new Exception\ExtensionNotLoadedException('This filter needs the zlib extension');
        }

        parent::__construct($options);
    }

    /**
     * Sets a new compression level
     *
     * @param int $level
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function setLevel($level)
    {
        if (($level < 0) || ($level > 9)) {
            throw new Exception\InvalidArgumentException('Level must be between 0 and 9');
        }

        $this->level = (int) $level;
    }

    /**
     * Returns the set compression level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets a new compression mode
     *
     * @param  string $compressionMode Supported are 'compress', 'deflate' and 'file'
     * @return void
     * @throws Exception\InvalidArgumentException for invalid $mode value
     */
    public function setCompressionMode($compressionMode)
    {
        if (!in_array($compressionMode, array(self::COMPRESSION_MODE_COMPRESS, self::COMPRESSION_MODE_DEFLATE))) {
            throw new Exception\InvalidArgumentException('Given compression mode not supported');
        }

        $this->compressionMode = $compressionMode;
    }

    /**
     * Returns the set compression mode
     *
     * @return string
     */
    public function getCompressionMode()
    {
        return $this->compressionMode;
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
     * Returns the set archive
     *
     * @return string
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        $archive = $this->getArchive();
        if (!empty($archive)) {
            $file = gzopen($archive, 'w' . $this->getLevel());

            if (!$file) {
                throw new Exception\RuntimeException("Error opening the archive $archive");
            }

            gzwrite($file, $content);
            gzclose($file);
            $compressed = true;
        } elseif ($this->compressionMode === self::COMPRESSION_MODE_DEFLATE) {
            $compressed = gzdeflate($content, $this->getLevel());
        } else {
            $compressed = gzcompress($content, $this->getLevel());
        }

        if (!$compressed) {
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
        $mode    = $this->getCompressionMode();

        if (file_exists($content)) {
            $archive = $content;
        }

        if (file_exists($archive)) {
            $handler = fopen($archive, "rb");
            if (!$handler) {
                throw new Exception\RuntimeException("Error opening the archive $archive");
            }

            fseek($handler, -4, SEEK_END);
            $packet = fread($handler, 4);
            $bytes  = unpack("V", $packet);
            $size   = end($bytes);
            fclose($handler);

            $file       = gzopen($archive, 'r');
            $compressed = gzread($file, $size);
            gzclose($file);
        } elseif ($mode === self::COMPRESSION_MODE_DEFLATE) {
            $compressed = gzinflate($content);
        } else {
            $compressed = gzuncompress($content);
        }

        if (!$compressed) {
            throw new Exception\RuntimeException('Error during decompression');
        }

        return $compressed;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return 'Gz';
    }
}
