<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator\File;

use Zend\Validator\Exception;

/**
 * Validator which checks if the destination file does not exist
 *
 * @category  Zend
 * @package   Zend_Validator
 */
class NotExists extends Exists
{
    /**
     * @const string Error constants
     */
    const DOES_EXIST = 'fileNotExistsDoesExist';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::DOES_EXIST => "File exists",
    );

    /**
     * Returns true if and only if the file does not exist in the set destinations
     *
     * @param  string|array $value Real file to check for existence
     * @return boolean
     */
    public function isValid($value)
    {
        if (is_array($value)) {
            if (!isset($value['tmp_name']) || !isset($value['name'])) {
                throw new Exception\InvalidArgumentException(
                    'Value array must be in $_FILES format'
                );
            }
            $file     = $value['tmp_name'];
            $filename = basename($file);
            $this->setValue($value['name']);
        } else {
            $file     = $value;
            $filename = basename($file);
            $this->setValue($filename);
        }

        $check = false;
        $directories = $this->getDirectory(true);
        if (!isset($directories)) {
            $check = true;
            if (file_exists($file)) {
                $this->error(self::DOES_EXIST);
                return false;
            }
        } else {
            foreach ($directories as $directory) {
                if (!isset($directory) || '' === $directory) {
                    continue;
                }

                $check = true;
                if (file_exists($directory . DIRECTORY_SEPARATOR . $filename)) {
                    $this->error(self::DOES_EXIST);
                    return false;
                }
            }
        }

        if (!$check) {
            $this->error(self::DOES_EXIST);
            return false;
        }

        return true;
    }
}
