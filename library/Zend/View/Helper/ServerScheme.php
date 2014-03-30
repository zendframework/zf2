<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper;

/**
 * Helper for returning the current scheme.
 */
class ServerScheme extends ServerUrl
{
    /**
     * View helper
     * Returns the current scheme http|https
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->getScheme();
    }
}
