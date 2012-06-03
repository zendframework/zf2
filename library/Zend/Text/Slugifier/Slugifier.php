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
 * @package    Zend_Text
 * @subpackage Slugifier
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Text\Slugifier;

use Zend\Text\UniDecoder\UniDecoder;

/**
 * Slugifier.
 * 
 * @category   Zend
 * @package    Zend_Text
 * @subpackage Slugifier
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Slugifier
{
    /**
     * Default UniDecoder instance.
     * 
     * @var UniDecode
     */
    protected static $defaultUniDecoder;
    
    /**
     * UniDecoder instance.
     * 
     * @var UniDecode
     */
    protected $uniDecoder;
    
    /**
     * Slugify a string.
     * 
     * @param  string $string
     * @return string
     */
    public function slugify($string)
    {
        $string = $this->uniDecoder()->decode($string);
        $string = strtolower($string);
        $string = str_replace("'", '', $string);
        $string = preg_replace('([^a-z0-9_-]+)', '-', $string);
        $string = preg_replace('(-{2,})', '-', $string);
        $string = trim($string, '-');
        
        return $string;
    }
    
    /**
     * Get or set uni decoder instance.
     * 
     * @return UniDecoder
     */
    public function uniDecoder(UniDecoder $decoder = null)
    {
        if ($decoder !== null) {
            $this->uniDecoder = $decoder;
        } elseif ($this->uniDecoder === null) {
            if (self::$defaultUniDecoder === null) {
                self::$defaultUniDecoder = new UniDecoder();
            }
            
            $this->uniDecoder = self::$defaultUniDecoder;
        }
        
        return $this->uniDecoder;
    }

    /**
     * Set the default uni decoder.
     *
     * @param  UniDecoder $decoder
     * @return void
     */
    public static function setDefaultUniDecoder(UniDecoder $decoder)
    {
        self::$defaultUniDecoder = $decoder;
    }
}
