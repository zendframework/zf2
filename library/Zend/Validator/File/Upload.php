<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator\File;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Validator for the maximum size of a file up to a max of 2GB
 *
 * @category  Zend
 * @package   Zend_Validator
 */
class Upload extends AbstractValidator
{
    /**
     * @const string Error constants
     */
    const INI_SIZE       = 'fileUploadErrorIniSize';
    const FORM_SIZE      = 'fileUploadErrorFormSize';
    const PARTIAL        = 'fileUploadErrorPartial';
    const NO_FILE        = 'fileUploadErrorNoFile';
    const NO_TMP_DIR     = 'fileUploadErrorNoTmpDir';
    const CANT_WRITE     = 'fileUploadErrorCantWrite';
    const EXTENSION      = 'fileUploadErrorExtension';
    const ATTACK         = 'fileUploadErrorAttack';
    const FILE_NOT_FOUND = 'fileUploadErrorFileNotFound';
    const UNKNOWN        = 'fileUploadErrorUnknown';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::INI_SIZE       => "File exceeds the defined ini size",
        self::FORM_SIZE      => "File exceeds the defined form size",
        self::PARTIAL        => "File was only partially uploaded",
        self::NO_FILE        => "File was not uploaded",
        self::NO_TMP_DIR     => "No temporary directory was found for file",
        self::CANT_WRITE     => "File can't be written",
        self::EXTENSION      => "A PHP extension returned an error while uploading the file",
        self::ATTACK         => "File was illegally uploaded. This could be a possible attack",
        self::FILE_NOT_FOUND => "File was not found",
        self::UNKNOWN        => "Unknown error while uploading file",
    );

    /**
     * Returns true if and only if the file was uploaded without errors
     *
     * @param  string $value File to check for upload errors
     * @return boolean
     */
    public function isValid($value)
    {
        if (is_array($value)) {
            if (!isset($value['tmp_name']) || !isset($value['name']) || !isset($value['error'])) {
                throw new Exception\InvalidArgumentException(
                    'Value array must be in $_FILES format'
                );
            }
            $file     = $value['tmp_name'];
            $filename = $value['name'];
            $error    = $value['error'];
        } else {
            $file     = $value;
            $filename = basename($file);
            $error    = 0;
        }
        $this->setValue($filename);

        if (false === stream_resolve_include_path($file)) {
            $this->error(self::FILE_NOT_FOUND);
            return false;
        }

        switch ($error) {
            case 0:
                if (!is_uploaded_file($file)) {
                    $this->error(self::ATTACK);
                }
                break;

            case 1:
                $this->error(self::INI_SIZE);
                break;

            case 2:
                $this->error(self::FORM_SIZE);
                break;

            case 3:
                $this->error(self::PARTIAL);
                break;

            case 4:
                $this->error(self::NO_FILE);
                break;

            case 6:
                $this->error(self::NO_TMP_DIR);
                break;

            case 7:
                $this->error(self::CANT_WRITE);
                break;

            case 8:
                $this->error(self::EXTENSION);
                break;

            default:
                $this->error(self::UNKNOWN);
                break;
        }

        if (count($this->getMessages()) > 0) {
            return false;
        }

        return true;
    }
}
