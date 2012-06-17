<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Zend\Http\Header;

/**
 * Accept Header
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
class Accept extends AbstractAccept
{
    protected $regexAddType = '#^([a-zA-Z+-]+|\*)/(\*|[a-zA-Z0-9+-]+)$#';

    /**
     * Get field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Accept';
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function toString()
    {
        return 'Accept: ' . $this->getFieldValue();
    }

    /**
     * Add a media type, with the given priority
     *
     * @param  string $type
     * @param  int|float $priority
     * @param  int $level
     * @return Accept
     */
    public function addMediaType($type, $priority = 1, array $params = array())
    {
        return $this->addType($type, $priority, $params);
    }

    /**
     * Does the header have the requested media type?
     *
     * @param  string $type
     * @return bool
     */
    public function hasMediaType($type)
    {
        return $this->hasType($type);
    }

    protected function getAcceptParamsFromMediaRangeString($mediaType)
    {
        $raw = $mediaType;
        if ($pos = strpos($mediaType, '/')) {
            $type = trim(substr($mediaType, 0, $pos));
        } else {
            $type = trim(substr($mediaType, 0));
        }

        $params = $this->parseMediaRanges($mediaType);

        if ($pos = strpos($mediaType, ';')) {
            $mediaType = trim(substr($mediaType, 0, $pos));
        }

        if ($pos = strpos($mediaType, '/')) {
            $subtypeWhole = $format = $subtype = trim(substr($mediaType, strpos($mediaType, '/')+1));
        } else {
            $subtypeWhole = '';
            $format = '*';
            $subtype = '*';
        }

        $pos = strpos($subtype, '+');
        if (false !== $pos) {
            $format = trim(substr($subtype, $pos+1));
            $subtype = trim(substr($subtype, 0, $pos));
        }

        return (object) array(
                'typeString' => trim($mediaType),
                'type'    => $type,
                'subtype' => $subtype,
                'subtypeRaw' => $subtypeWhole,
                'format'  => $format,
                'priority' => isset($params['q']) ? $params['q'] : 1,
                'params' => $params,
                'raw' => trim($raw)
        );
    }
}
