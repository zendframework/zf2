<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Barcode\Object;

/**
 * Class for generate Ean2 barcode
 */
class Ean2 extends Ean5
{
    protected $parities = [
        0 => ['A','A'],
        1 => ['A','B'],
        2 => ['B','A'],
        3 => ['B','B']
    ];

    /**
     * Default options for Ean2 barcode
     * @return void
     */
    protected function getDefaultOptions()
    {
        $this->barcodeLength = 2;
    }

    protected function getParity($i)
    {
        $modulo = $this->getText() % 4;
        return $this->parities[$modulo][$i];
    }
}
