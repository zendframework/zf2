<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Storage\Directory;

use Zend\Search\Lucene;
use Zend\Search\Lucene\Storage\Directory;
use Zend\Search\Lucene\Storage\File;
use Zend\Stdlib\ErrorHandler;

/**
 * FileSystem implementation of DirectoryInterface abstraction.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Storage
 */
class Filesystem implements DirectoryInterface
{
    /**
     * Filesystem path to the directory
     *
     * @var string
     */
    protected $_dirPath = null;

    /**
     * Cache for Zend_Search_Lucene_Storage_File_Filesystem objects
     * Array: filename => Zend_Search_Lucene_Storage_File object
     *
     * @var array
     * @throws \Zend\Search\Lucene\Exception\ExceptionInterface
     */
    protected $_fileHandlers;

    /**
     * Default file permissions
     *
     * @var integer
     */
    protected static $_defaultFilePermissions = 0666;


    /**
     * Get default file permissions
     *
     * @return integer
     */
    public static function getDefaultFilePermissions()
    {
        return self::$_defaultFilePermissions;
    }

    /**
     * Set default file permissions
     *
     * @param integer $mode
     */
    public static function setDefaultFilePermissions($mode)
    {
        self::$_defaultFilePermissions = $mode;
    }


    /**
     * Utility function to recursive directory creation
     *
     * @param string $dir
     * @param integer $mode
     * @param boolean $recursive
     * @return boolean
     */

    public static function mkdirs($dir, $mode = 0777, $recursive = true)
    {
        if (($dir === null) || $dir === '') {
            return false;
        }
        if (is_dir($dir) || $dir === '/') {
            return true;
        }
        if (self::mkdirs(dirname($dir), $mode, $recursive)) {
            return mkdir($dir, $mode);
        }
        return false;
    }


    /**
     * Object constructor
     * Checks if $path is a directory or tries to create it.
     *
     * @param string $path
     * @throws \Zend\Search\Lucene\Exception\InvalidArgumentException
     */
    public function __construct($path)
    {
        if (!is_dir($path)) {
            if (file_exists($path)) {
                throw new Lucene\Exception\InvalidArgumentException(
                	'Path exists, but it\'s not a directory'
                );
            } else {
                if (!self::mkdirs($path)) {
                    throw new Lucene\Exception\InvalidArgumentException(
                    	"Can't create directory '$path'."
                    );
                }
            }
        }
        $this->_dirPath = $path;
        $this->_fileHandlers = array();
    }


    /**
     * Closes the store.
     *
     * @return void
     */
    public function close()
    {
        foreach ($this->_fileHandlers as $fileObject) {
            $fileObject->close();
        }

        $this->_fileHandlers = array();
    }


    /**
     * Returns an array of strings, one for each file in the directory.
     *
     * @return array
     */
    public function fileList()
    {
        $result = array();

        $dirContent = opendir( $this->_dirPath );
        while (($file = readdir($dirContent)) !== false) {
            if (($file == '..')||($file == '.'))   continue;

            if( !is_dir($this->_dirPath . '/' . $file) ) {
                $result[] = $file;
            }
        }
        closedir($dirContent);

        return $result;
    }

    /**
     * Creates a new, empty file in the directory with the given $filename.
     *
     * @param string $filename
     * @return \Zend\Search\Lucene\Storage\File\FileInterface
     */
    public function createFile($filename)
    {
        if (isset($this->_fileHandlers[$filename])) {
            $this->_fileHandlers[$filename]->close();
        }
        unset($this->_fileHandlers[$filename]);
        $this->_fileHandlers[$filename] = new File\Filesystem($this->_dirPath . '/' . $filename, 'w+b');

        // Set file permissions, but don't care about any possible failures, since file may be already
        // created by anther user which has to care about right permissions
        ErrorHandler::start(E_WARNING);
        chmod($this->_dirPath . '/' . $filename, self::$_defaultFilePermissions);
        ErrorHandler::stop();

        return $this->_fileHandlers[$filename];
    }


    /**
     * Removes an existing $filename in the directory.
     *
     * @param string $filename
     * @throws \Zend\Search\Lucene\Exception\RuntimeException
     * @return void
     */
    public function deleteFile($filename)
    {
        if (isset($this->_fileHandlers[$filename])) {
            $this->_fileHandlers[$filename]->close();
        }
        unset($this->_fileHandlers[$filename]);

        global $php_errormsg;
        $trackErrors = ini_get('track_errors'); ini_set('track_errors', '1');
        if (!@unlink($this->_dirPath . '/' . $filename)) {
            ini_set('track_errors', $trackErrors);
            throw new Lucene\Exception\RuntimeException('Can\'t delete file: ' . $php_errormsg);
        }
        ini_set('track_errors', $trackErrors);
    }

    /**
     * Purge file if it's cached by directory object
     *
     * Method is used to prevent 'too many open files' error
     *
     * @param string $filename
     * @return void
     */
    public function purgeFile($filename)
    {
        if (isset($this->_fileHandlers[$filename])) {
            $this->_fileHandlers[$filename]->close();
        }
        unset($this->_fileHandlers[$filename]);
    }


    /**
     * Returns true if a file with the given $filename exists.
     *
     * @param string $filename
     * @return boolean
     */
    public function fileExists($filename)
    {
        return isset($this->_fileHandlers[$filename]) ||
               file_exists($this->_dirPath . '/' . $filename);
    }


    /**
     * Returns the length of a $filename in the directory.
     *
     * @param string $filename
     * @return integer
     */
    public function fileLength($filename)
    {
        if (isset( $this->_fileHandlers[$filename] )) {
            return $this->_fileHandlers[$filename]->size();
        }
        return filesize($this->_dirPath .'/'. $filename);
    }


    /**
     * Returns the UNIX timestamp $filename was last modified.
     *
     * @param string $filename
     * @return integer
     */
    public function fileModified($filename)
    {
        return filemtime($this->_dirPath .'/'. $filename);
    }


    /**
     * Renames an existing file in the directory.
     *
     * @param string $from
     * @param string $to
     * @throws \Zend\Search\Lucene\Exception\RuntimeException
     * @return void
     */
    public function renameFile($from, $to)
    {
        global $php_errormsg;

        if (isset($this->_fileHandlers[$from])) {
            $this->_fileHandlers[$from]->close();
        }
        unset($this->_fileHandlers[$from]);

        if (isset($this->_fileHandlers[$to])) {
            $this->_fileHandlers[$to]->close();
        }
        unset($this->_fileHandlers[$to]);

        if (file_exists($this->_dirPath . '/' . $to)) {
            if (!unlink($this->_dirPath . '/' . $to)) {
                throw new Lucene\Exception\RuntimeException(
                	'Delete operation failed'
                );
            }
        }

        $trackErrors = ini_get('track_errors');
        ini_set('track_errors', '1');

        ErrorHandler::start(E_WARNING);
        $success = rename($this->_dirPath . '/' . $from, $this->_dirPath . '/' . $to);
        ErrorHandler::stop();
        if (!$success) {
            ini_set('track_errors', $trackErrors);
            throw new Lucene\Exception\RuntimeException($php_errormsg);
        }

        ini_set('track_errors', $trackErrors);

        return $success;
    }


    /**
     * Sets the modified time of $filename to now.
     *
     * @param string $filename
     * @return void
     */
    public function touchFile($filename)
    {
        return touch($this->_dirPath .'/'. $filename);
    }


    /**
     * Returns a Zend_Search_Lucene_Storage_File object for a given $filename in the directory.
     *
     * If $shareHandler option is true, then file handler can be shared between File Object
     * requests. It speed-ups performance, but makes problems with file position.
     * Shared handler are good for short atomic requests.
     * Non-shared handlers are useful for stream file reading (especial for compound files).
     *
     * @param string $filename
     * @param boolean $shareHandler
     * @return \Zend\Search\Lucene\Storage\File\FileInterface
     */
    public function getFileObject($filename, $shareHandler = true)
    {
        $fullFilename = $this->_dirPath . '/' . $filename;

        if (!$shareHandler) {
            return new File\Filesystem($fullFilename);
        }

        if (isset( $this->_fileHandlers[$filename] )) {
            $this->_fileHandlers[$filename]->seek(0);
            return $this->_fileHandlers[$filename];
        }

        $this->_fileHandlers[$filename] = new File\Filesystem($fullFilename);
        return $this->_fileHandlers[$filename];
    }
}
