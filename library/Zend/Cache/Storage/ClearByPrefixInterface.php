<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace Zend\Cache\Storage;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 */
interface ClearByPrefixInterface
{
    /**
     * Remove items matching given prefix
     *
     * @param string $prefix
     * @return boolean
     */
    public function clearByPrefix($prefix);
}
