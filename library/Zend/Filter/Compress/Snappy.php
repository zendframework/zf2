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
 * Compression adapter for php snappy (http://code.google.com/p/php-snappy/)
 */
class Snappy implements CompressionAlgorithmInterface
{
    /**
     * Class constructor
     *
     * @throws Exception\ExtensionNotLoadedException if snappy extension not loaded
     */
    public function __construct()
    {
        if (!extension_loaded('snappy')) {
            throw new Exception\ExtensionNotLoadedException('This filter needs the snappy extension');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        $compressed = snappy_compress($content);

        if ($compressed === false) {
            throw new Exception\RuntimeException('Error while compressing.');
        }

        return $compressed;
    }

    /**
     * {@inheritDoc}
     */
    public function decompress($content)
    {
        $compressed = snappy_uncompress($content);

        if ($compressed === false) {
            throw new Exception\RuntimeException('Error while decompressing.');
        }

        return $compressed;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return 'Snappy';
    }
}
