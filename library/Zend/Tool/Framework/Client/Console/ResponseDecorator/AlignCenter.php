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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Client\Console\ResponseDecorator;

/**
 * Try to align a given text central on the screen.
 *
 * @uses       \Zend\Tool\Framework\Client\Response\ContentDecorator
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AlignCenter
    implements \Zend\Tool\Framework\Client\Response\ContentDecorator
{
    public function getName()
    {
        return "aligncenter";
    }

    /**
     * @param string $content
     * @param integer $lineLength
     */
    public function decorate($content, $lineLength)
    {
        if(!is_numeric($lineLength)) {
            $lineLength = 72;
        }
        if(strlen($content) < $lineLength) {
            $append = false;
            $len = strlen($content);
            for($i = $len; $i < $lineLength; $i++) {
                if($append == true) {
                    $content = $content." ";
                    $append = false;
                } else {
                    $content = " ".$content;
                    $append = true;
                }
            }
        }
        return $content;
    }
}
