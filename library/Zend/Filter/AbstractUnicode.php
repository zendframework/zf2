<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

abstract class AbstractUnicode extends AbstractFilter
{
    /**
     * @var string
     */
    protected $encoding;

    /**
     * Class constructor
     *
     * @param  array|null $options
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('mbstring')) {
            throw new Exception\ExtensionNotLoadedException(sprintf(
                'This filter ("%s") needs the mbstring extension',
                get_class($this)
            ));
        }

        parent::__construct($options);
    }

    /**
     * Set the input encoding for the given string
     *
     * @param  string $encoding
     * @return void
     * @throws Exception\InvalidArgumentException
     * @throws Exception\ExtensionNotLoadedException
     */
    public function setEncoding($encoding)
    {
        $encoding    = strtolower($encoding);
        $mbEncodings = array_map('strtolower', mb_list_encodings());
        if (!in_array($encoding, $mbEncodings)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Encoding "%s" is not supported by mbstring extension',
                $encoding
            ));
        }

        $this->encoding = $encoding;
    }

    /**
     * Returns the set encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        if (null === $this->encoding && function_exists('mb_internal_encoding')) {
            $this->encoding['encoding'] = mb_internal_encoding();
        }

        return $this->encoding;
    }
}
