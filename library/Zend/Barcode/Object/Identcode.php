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
 * @package    Zend_Barcode
 * @subpackage Object
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Barcode\Object;

/**
 * Class for generate Identcode barcode
 *
 * @uses        \Zend\Barcode\Object\Code25interleaved
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Identcode extends Code25interleaved
{

    /**
     * Default options for Identcode barcode
     * @return void
     */
    protected function getDefaultOptions()
    {
        $this->barcodeLength = 12;
        $this->mandatoryChecksum = true;
    }

    /**
     * Retrieve text to display
     * @return string
     */
    public function getTextToDisplay()
    {
        return preg_replace('/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9])/',
                            '$1.$2 $3.$4 $5',
                            $this->getText());
    }

    /**
     * Check allowed characters
     * @param string $value
     * @return string
     * @throw \Zend\Barcode\Object\Exception
     */
    public function validateText($value)
    {
        $this->validateSpecificText($value, array('validator' => $this->getType()));
    }

    /**
     * Get barcode checksum
     *
     * @param  string $text
     * @return int
     */
    public function getChecksum($text)
    {
        $this->checkText($text);
        $checksum = 0;

        for ($i = strlen($text); $i > 0; $i --) {
            $checksum += intval($text{$i - 1}) * (($i % 2) ? 4 : 9);
        }

        $checksum = (10 - ($checksum % 10)) % 10;

        return $checksum;
    }
}
