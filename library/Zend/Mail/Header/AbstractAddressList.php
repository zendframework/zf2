<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Header;

use Zend\Mail\AddressList;
use Zend\Mail\Headers;

/**
 * Base class for headers composing address lists (to, from, cc, bcc, reply-to)
 */
abstract class AbstractAddressList implements HeaderInterface
{
    /**
     * @var AddressList
     */
    protected $addressList;

    /**
     * @var string Normalized field name
     */
    protected $fieldName;

    /**
     * Header encoding
     *
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * @var string lower case field name
     */
    protected static $type;

    public static function fromString($headerLine)
    {
        list($fieldName, $fieldValue) = GenericHeader::splitHeaderLine($headerLine);

        if (strtolower($fieldName) !== static::$type) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid header line for "%s" string',
                __CLASS__
            ));
        }
        $header = new static();

        //TODO: haven't we already unfolded the string when we get here?
        $fieldValue = str_replace(Headers::FOLDING, ' ', $fieldValue);
        // split value on ","
        $values     = str_getcsv($fieldValue, ',');

        $addressList = $header->getAddressList();
        foreach ($values as $address) {
            $address = trim($address);
            //we should not error when we have an empty header like 'Reply-To: '
            if (empty($address)) {
                continue;
            }
            $decoded = iconv_mime_decode($address, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
            if ($decoded != $address) {
                $header->setEncoding('UTF-8');
            }
            $addressList->addFromString($decoded);
        }
        return $header;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        $emails   = array();
        $encoding = $this->getEncoding();
        foreach ($this->getAddressList() as $address) {
            $email = $address->getEmail();
            $name  = $address->getName();
            if (empty($name)) {
                $emails[] = $email;
            } else {
                if ($format == HeaderInterface::FORMAT_ENCODED
                    && 'ASCII' !== $encoding
                ) {
                    $name = HeaderWrap::mimeEncodeValue($name, $encoding);
                } elseif (false !== strpos($name, ',')) {
                    $name = sprintf('"%s"', $name);
                }
                $emails[] = sprintf('%s <%s>', $name, $email);
            }
        }

        return implode(',' . Headers::FOLDING, $emails);
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

    /**
     * Set address list for this header
     *
     * @param  AddressList $addressList
     */
    public function setAddressList(AddressList $addressList)
    {
        $this->addressList = $addressList;
    }

    /**
     * Get address list managed by this header
     *
     * @return AddressList
     */
    public function getAddressList()
    {
        if (null === $this->addressList) {
            $this->setAddressList(new AddressList());
        }
        return $this->addressList;
    }

    public function toString()
    {
        $name  = $this->getFieldName();
        $value = $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
        return (empty($value)) ? '' : sprintf('%s: %s', $name, $value);
    }
}
