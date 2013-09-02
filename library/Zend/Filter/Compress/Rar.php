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
 * Compression adapter for Rar
 */
class Rar extends AbstractCompressionAdapter
{
    /**
     * @var Callable
     */
    protected $callback;

    /**
     * @var string
     */
    protected $archive;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $target = '.';

    /**
     * Class constructor
     *
     * @param array $options (Optional) Options to set
     * @throws Exception\ExtensionNotLoadedException if rar extension not loaded
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('rar')) {
            throw new Exception\ExtensionNotLoadedException('This filter needs the rar extension');
        }

        parent::__construct($options);
    }

    /**
     * Sets the callback to use
     *
     * @param  Callable $callback
     * @return void
     */
    public function setCallback(Callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Returns the set callback for compression
     *
     * @return Callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets the archive to use for de-/compression
     *
     * @param  string $archive Archive to use
     * @return void
     */
    public function setArchive($archive)
    {
        $this->archive = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $archive);
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
     * Sets the password to use
     *
     * @param  string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;
    }

    /**
     * Returns the set password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the target path to use
     *
     * @param  string $target
     * @return void
     * @throws Exception\InvalidArgumentException if specified target directory does not exist
     */
    public function setTarget($target)
    {
        if (!file_exists(dirname($target))) {
            throw new Exception\InvalidArgumentException('The directory "$target" does not exist');
        }

        $this->target = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, (string) $target);
    }

    /**
     * Returns the set targetpath
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        $callback = $this->getCallback();
        if (null === $callback) {
            throw new Exception\RuntimeException('No compression callback available');
        }

        $options = array(
            'archive'  => $this->archive,
            'password' => $this->password,
            'target'   => $this->target
        );

        $result = $callback($options, $content);

        if ($result !== true) {
            throw new Exception\RuntimeException('Error compressing the RAR Archive');
        }

        return $this->getArchive();
    }

    /**
     * {@inheritDoc}
     */
    public function decompress($content)
    {
        if (!file_exists($content)) {
            throw new Exception\RuntimeException('RAR Archive not found');
        }

        $archive  = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, realpath($content));
        $password = $this->getPassword();

        if (null !== $password) {
            $archive = rar_open($archive, $password);
        } else {
            $archive = rar_open($archive);
        }

        if (!$archive) {
            throw new Exception\RuntimeException("Error opening the RAR Archive");
        }

        $target = $this->getTarget();
        if (!is_dir($target)) {
            $target = dirname($target);
        }

        $filelist = rar_list($archive);
        if (!$filelist) {
            throw new Exception\RuntimeException("Error reading the RAR Archive");
        }

        foreach ($filelist as $file) {
            $file->extract($target);
        }

        rar_close($archive);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return 'Rar';
    }
}
