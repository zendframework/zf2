<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.19
 */
class Etag implements HeaderInterface
{
    protected $value;

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(':', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower(trim($name)) !== 'etag') {
            throw new Exception\InvalidArgumentException('Invalid header line for Etag string: "' . $name . '"');
        }

        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'Etag';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Etag: ' . $this->getFieldValue();
    }
}
