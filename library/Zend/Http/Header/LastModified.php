<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Http\Header;

/**
 * Last-Modified Header
 *
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.29
 */
class LastModified extends AbstractDate
{
    /**
     * Get header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Last-Modified';
    }
}
