<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Header;

use Zend\Mail\Headers;

/**
 * @todo       Allow setting date from DateTime, Zend\Date, or string
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 */
class Received implements HeaderInterface, MultipleHeadersInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Header encoding
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

    public static function fromString($headerLine)
    {
        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'received') {
            throw new Exception\InvalidArgumentException('Invalid header line for Received string');
        }

        $header = new static();
        $header->value= $value;
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Received';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->value;
    }

    public function setEncoding($encoding) 
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function toString()
    {
        return 'Received: ' . $this->getFieldValue(HeaderInterface::FORMAT_RAW);
    }

    /**
     * Serialize collection of Received headers to string
     *
     * @param  array $headers
     * @throws Exception\RuntimeException
     * @return string
     */
    public function toStringMultipleHeaders(array $headers)
    {
        $strings = array($this->toString());
        foreach ($headers as $header) {
            if (!$header instanceof Received) {
                throw new Exception\RuntimeException(
                    'The Received multiple header implementation can only accept an array of Received headers'
                );
            }
            $strings[] = $header->toString();
        }
        return implode(Headers::EOL, $strings);
    }
}
