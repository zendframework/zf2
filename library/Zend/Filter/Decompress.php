<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Zend\Stdlib\AbstractOptions;

/**
 * Decompresses a given string
 */
class Decompress extends Compress
{
    /**
     * Decompresses the content $value with the defined settings
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $adapter = $this->getAdapter();
        if (($adapterOptions = $this->getAdapterOptions()) && $adapter instanceof AbstractOptions) {
            $adapter->setFromArray($adapterOptions);
        }

        return $this->getAdapter()->decompress($value);
    }
}
